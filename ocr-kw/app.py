"""
OCR Microservice - FastAPI + EasyOCR
Accepts JPG receipt uploads and returns parsed line items with confidence scores.
"""

from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import easyocr
import numpy as np
import cv2
from typing import List, Dict, Optional
import io
import uvicorn
import logging
import re
import json
import os
from datetime import datetime

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="OCR Microservice",
    description="Receipt OCR service using EasyOCR",
    version="1.0.0"
)

# Add CORS middleware for web integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Global OCR reader - initialized on startup
ocr_reader = None

@app.on_event("startup")
async def startup_event():
    """Initialize EasyOCR reader on startup"""
    global ocr_reader
    logger.info("Initializing EasyOCR reader...")    # Initialize with English and Indonesian languages, no GPU
    ocr_reader = easyocr.Reader(['en', 'id'], gpu=False)
    logger.info("EasyOCR reader initialized successfully")

@app.get("/")
async def root():
    """Health check endpoint"""
    return {"status": "OCR service is running", "version": "1.0.0"}

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "ocr_ready": ocr_reader is not None}

@app.post("/ocr")
async def extract_text(file: UploadFile = File(...)):
    """
    Extract text from uploaded image using EasyOCR
    
    Args:
        file: Uploaded image file (JPG, PNG, etc.)
        
    Returns:
        JSON with extracted text lines and confidence scores
    """
    if ocr_reader is None:
        raise HTTPException(status_code=503, detail="OCR service not ready")
    
    # Validate file type
    if not file.content_type or not file.content_type.startswith('image/'):
        raise HTTPException(status_code=400, detail="File must be an image")
    
    try:
        # Read image file
        image_data = await file.read()
        
        # Convert to numpy array
        nparr = np.frombuffer(image_data, np.uint8)
        image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if image is None:
            raise HTTPException(status_code=400, detail="Invalid image file")
        
        # Perform OCR
        logger.info(f"Processing image: {file.filename}")
        results = ocr_reader.readtext(image)
        
        # Format results
        lines = []
        for (bbox, text, confidence) in results:
            lines.append({
                "text": text.strip(),
                "confidence": round(confidence, 4)
            })
        
        logger.info(f"Extracted {len(lines)} text lines")
        return {"lines": lines}
        
    except Exception as e:
        logger.error(f"OCR processing error: {str(e)}")
        raise HTTPException(status_code=500, detail=f"OCR processing failed: {str(e)}")

@app.post("/ocr/parse")
async def parse_receipt_items(file: UploadFile = File(...)):
    """
    Parse receipt line items with intelligent description + amount extraction
    
    Args:
        file: Uploaded receipt image file
        
    Returns:
        JSON with parsed line items (description + amount pairs)
    """
    if ocr_reader is None:
        raise HTTPException(status_code=503, detail="OCR service not ready")
    
    # Validate file type
    if not file.content_type or not file.content_type.startswith('image/'):
        raise HTTPException(status_code=400, detail="File must be an image")
    
    try:
        # Read image file
        image_data = await file.read()
        
        # Convert to numpy array
        nparr = np.frombuffer(image_data, np.uint8)
        image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if image is None:
            raise HTTPException(status_code=400, detail="Invalid image file")
        
        # Perform OCR
        logger.info(f"Processing receipt for line items: {file.filename}")
        results = ocr_reader.readtext(image)        # Extract and parse line items
        parsed_items = parse_line_items(results)
        
        logger.info(f"Parsed {len(parsed_items)} line items from {len(results)} raw lines")
        
        # Create response data
        response_data = {
            "success": True,
            "processing_info": {
                "filename": file.filename,
                "total_items": len(parsed_items),
                "raw_lines_detected": len(results),
                "processing_time": None,
                "timestamp": datetime.now().isoformat()
            },
            "line_items": parsed_items
        }
        
        # Export to JSON file
        await export_to_json(response_data, file.filename)
        
        # Return structured JSON response
        return response_data
        
    except Exception as e:
        logger.error(f"Receipt parsing error: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Receipt parsing failed: {str(e)}")

def parse_line_items(ocr_results: List) -> List[Dict]:
    """
    Parse OCR results into line items with descriptions and amounts
    
    Args:
        ocr_results: Raw OCR results from EasyOCR
        
    Returns:
        List of parsed line items with description and amount
    """
    # Clear any previous state and initialize fresh for each image
    line_items = []
    processed_texts = set()  # Track processed text to avoid duplicates
      # Enhanced regex patterns for receipt price detection
    price_patterns = [
        # US Dollar formats
        r'\$\s*(\d+[\.,]\d{2})',                    # $12.34, $ 12.34
        r'(\d+[\.,]\d{2})\s*\$',                    # 12.34$
        
        # Indonesian Rupiah formats
        r'Rp\s*(\d+[\.,]\d{3})',                    # Rp 12.000, Rp12.000
        r'Rp\s*(\d+)[\.,](\d{3})[\.,](\d{3})',      # Rp 1.000.000, Rp1,000,000
        r'(\d+[\.,]\d{3})\s*Rp',                    # 12.000 Rp
        r'(\d+)[\.,](\d{3})[\.,](\d{3})\s*Rp',      # 1.000.000 Rp
        
        # Generic number formats (common in receipts)
        r'(\d+[\.,]\d{2,3})(?=\s|$)',               # 12.34, 12.345 (standalone)
        r'(\d+)\s*[\.,]\s*(\d{2,3})',               # 12 . 34, 12 , 345
        r'(\d+)\.(\d{3})(?=\s|$)',                  # 12.000 format
        r'(\d+)[\.,](\d{3})[\.,](\d{3})(?=\s|$)',   # 1.000.000 or 1,000,000 format
    ]
    
    # Combine all patterns
    combined_pattern = '|'.join(price_patterns)
    
    # Extract text lines with positions
    lines = []
    for (bbox, text, confidence) in ocr_results:
        # Skip empty or whitespace-only text
        cleaned_text = text.strip()
        if not cleaned_text or len(cleaned_text) < 1:
            continue
            
        # Clean up common OCR errors in numbers
        cleaned_text = clean_ocr_text(cleaned_text)
        
        # Get vertical position (y-coordinate) for sorting
        y_pos = bbox[0][1]  # Top-left y coordinate
        
        lines.append({
            'text': cleaned_text,
            'confidence': confidence,
            'y_pos': y_pos,
            'bbox': bbox
        })
    
    # Sort lines by vertical position (top to bottom)
    lines.sort(key=lambda x: x['y_pos'])
    
    # Parse line items    
    for i, line in enumerate(lines):
        text = line['text']
        
        # Skip very short text or low confidence
        if len(text) < 2 or line['confidence'] < 0.3:
            continue
            
        # Skip if we've already processed this exact text
        if text.lower() in processed_texts:
            continue
            
        # Skip common header/footer words
        skip_words = ['receipt', 'total', 'subtotal', 'tax', 'change', 'thank', 'you', 
                     'cash', 'card', 'visa', 'mastercard', 'date', 'time', 'store']
        if any(skip_word in text.lower() for skip_word in skip_words):
            continue
          # Check if this line contains a price
        price_match = re.search(combined_pattern, text, re.IGNORECASE)
        
        if price_match:
            # Extract the price - handle different currency formats
            price_str = None
            amount = 0.0
            
            # Get the full matched text to determine format
            full_match = price_match.group(0)
            
            # Check if it's a Rupiah format
            if 'Rp' in full_match or 'rp' in full_match.lower():
                # Indonesian Rupiah format - extract numeric part
                numeric_part = re.sub(r'[Rp\s]', '', full_match, flags=re.IGNORECASE)
                
                # Handle different Rupiah formats
                if '.' in numeric_part and ',' in numeric_part:
                    # Format like 1.000.000,50 or 1,000,000.50
                    if numeric_part.rfind('.') > numeric_part.rfind(','):
                        # Last separator is dot (decimal)
                        amount = float(numeric_part.replace(',', ''))
                    else:
                        # Last separator is comma (decimal)
                        amount = float(numeric_part.replace('.', '').replace(',', '.'))
                elif '.' in numeric_part:
                    # Could be thousands separator (12.000) or decimal (12.50)
                    parts = numeric_part.split('.')
                    if len(parts[-1]) == 3:  # 12.000 format (thousands)
                        amount = float(numeric_part.replace('.', ''))
                    else:  # 12.50 format (decimal)
                        amount = float(numeric_part)
                elif ',' in numeric_part:
                    # Comma as thousands separator or decimal
                    parts = numeric_part.split(',')
                    if len(parts[-1]) == 3:  # 12,000 format (thousands)
                        amount = float(numeric_part.replace(',', ''))
                    else:  # 12,50 format (decimal)
                        amount = float(numeric_part.replace(',', '.'))
                else:
                    # Plain number
                    amount = float(numeric_part)
            else:
                # Standard dollar or generic format
                for group in price_match.groups():
                    if group and group.replace('.', '').replace(',', '').isdigit():
                        price_str = group
                        break
                
                if price_str:
                    # Clean up price format - assume decimal format for non-Rupiah
                    amount = float(price_str.replace(',', '.'))
            
            if amount > 0:
                
                # Extract description (text before the price)
                description = text[:price_match.start()].strip()
                
                # If description is empty or too short, look at previous lines
                if len(description) < 3 and i > 0:
                    # Look at previous line(s) for description
                    for j in range(max(0, i-2), i):
                        prev_text = lines[j]['text'].strip()
                        # Check if previous line doesn't contain price and is long enough
                        if not re.search(combined_pattern, prev_text) and len(prev_text) > 2:
                            description = prev_text
                            break
                
                # Clean up description - remove special characters but keep basic punctuation
                description = re.sub(r'[^\w\s\-\(\)]', ' ', description)
                description = ' '.join(description.split())  # Remove extra spaces
                
                # Only add if we have a meaningful description and haven't seen this text
                if len(description) > 2 and text.lower() not in processed_texts:
                    processed_texts.add(text.lower())
                    line_items.append({
                        'description': description,
                        'amount': round(amount, 2),  # Round to 2 decimal places for JSON
                        'confidence': round(line['confidence'], 4),
                        'raw_text': text,
                        'position': {
                            'y': int(line['y_pos']),
                            'bbox': [[int(coord) for coord in point] for point in line['bbox']]
                        }
                    })
    
    # Remove duplicates and filter reasonable amounts
    filtered_items = []
    seen_descriptions = set()
    
    for item in line_items:
        desc_lower = item['description'].lower()
        
        # Skip if already seen similar description
        if desc_lower in seen_descriptions:
            continue
              # Filter reasonable price ranges 
        # USD: 0.01 to 999.99, IDR: 100 to 10,000,000 (roughly $0.01 to $700)
        if (0.01 <= item['amount'] <= 999.99) or (100 <= item['amount'] <= 10000000):
            seen_descriptions.add(desc_lower)
            filtered_items.append(item)
    return filtered_items

def clean_ocr_text(text: str) -> str:
    replacements = {
        'O': '0',    # Letter O -> Zero
        'o': '0',    # Lowercase o -> Zero
        'I': '1',    # Letter I -> One
        'l': '1',    # Lowercase l -> One
        'S': '5',    # Letter S -> Five (in some contexts)
        'B': '8',    # Letter B -> Eight (in some contexts)
        'G': '6',    # Letter G -> Six (in some contexts)
        'Z': '2',    # Letter Z -> Two (in some contexts)
    }    # Clean text for number patterns
    cleaned = text
    
    # Simple character replacements for common OCR errors
    for old, new in replacements.items():
        if re.search(r'\d', text):  # Only if text contains digits
            cleaned = cleaned.replace(old, new)
    
    return cleaned

async def export_to_json(data: dict, filename: str) -> None:
    """
    Export OCR results to a JSON file
    
    Args:
        data: The OCR result data to export
        filename: Original filename to base the export name on
    """
    try:
        # Create exports directory if it doesn't exist
        exports_dir = "exports"
        if not os.path.exists(exports_dir):
            os.makedirs(exports_dir)
        
        # Generate export filename
        base_name = os.path.splitext(filename)[0] if filename else "ocr_result"
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        export_filename = f"{exports_dir}/{base_name}_{timestamp}.json"
        
        # Write JSON file
        with open(export_filename, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        
        logger.info(f"OCR results exported to: {export_filename}")
        
    except Exception as e:
        logger.error(f"Failed to export JSON: {str(e)}")

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
