@extends('layout')

@section('content')
<style>
    .back-arrow {
        text-decoration: none;
        color: black;
        font-weight: bold;
        font-size: 24px;
        padding: 10px;
    }
    
    .page-header {
        padding: 20px 20px 0;
    }
    
    .page-title {
        color: #6b8e23;
        font-size: 18px;
        margin: 5px 0;
        font-weight: 600;
    }
    
    .page-subtitle {
        font-size: 24px;
        font-weight: bold;
        margin: 5px 0 10px;
    }
    
    .page-description {
        font-size: 14px;
        color: #444;
        margin-bottom: 20px;
    }
    
    .bill-container {
        padding: 0 20px;
    }
    
    .bill-owner {
        font-size: 15px;
        font-weight: 500;
        margin: 10px 0;
        color: #333;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 5px;
    }
    
    .bill-items {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .item-row {
        position: relative;
        margin-bottom: 15px;
    }
    
    .item-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .item-details {
        display: flex;
        gap: 10px;
    }
    
    .price-input, .qty-input, .total-input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .price-input {
        width: 30%;
    }
    
    .qty-input {
        width: 15%;
        text-align: center;
    }
    
    .total-input {
        flex-grow: 1;
    }
    
    .delete-btn {
        position: absolute;
        right: -10px;
        top: -10px;
        width: 24px;
        height: 24px;
        background-color: #7d9d61;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border: none;
    }
    
    .delete-btn:hover {
        background-color: #6b8e23;
    }
    
    .add-item-btn {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 5px;
        border-top: 1px dashed #ddd;
        margin-top: 10px;
        cursor: pointer;
    }
    
    .add-item-btn span {
        display: inline-block;
        width: 24px;
        height: 24px;
        background-color: #f0f0f0;
        border-radius: 50%;
        text-align: center;
        line-height: 24px;
        font-weight: bold;
        color: #555;
    }
    
    .summary-section {
        margin-top: 30px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
    }
    
    .summary-title {
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        align-items: center;
    }
    
    .summary-label {
        font-size: 14px;
    }
    
    .summary-value {
        width: 150px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: right;
    }
    
    .confirm-btn {
        background-color: #7d9d61;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        font-weight: bold;
        width: 100%;
        margin: 20px 0;
        cursor: pointer;
        text-transform: uppercase;
    }
</style>

<div class="page-header">
    <a href="/home" class="back-arrow">←</a>
    <div class="page-title">Our Bill</div>
    <div class="page-subtitle">EDIT BILL ITEMS</div>
    <div class="page-description">You can edit the title, amount and price of each item</div>
</div>

<form action="{{ route('bill2') }}" method="POST">
    @csrf
    <div class="bill-container">
        <div class="bill-owner">---'s Bill</div>
        
        <div class="bill-items" id="bill-items-container">
            @if($items)
                @foreach($items as $index => $item)
                    <div class="item-row">
                    <button type="button" class="delete-btn" onclick="deleteItemRow(this)">🗑️</button>
                    <input type="text" name="items[{{ $index }}][name]" class="item-input" placeholder="Name Item" value="{{ $item['name'] ?? '' }}" required>
                    <div class="item-details">
                        <input type="number" name="items[{{ $index }}][price]" class="price-input" placeholder="Price" onchange="calculateTotal(this)" data-type="price" value="{{ $item['price'] ?? '' }}" required>
                        <input type="number" name="items[{{ $index }}][qty]" class="qty-input" placeholder="Qty" onchange="calculateTotal(this)" data-type="qty" value="{{ $item['qty'] ?? 1 }}" required>
                        <input type="number" name="items[{{ $index }}][total]" class="total-input" placeholder="Total Price" data-type="total" value="{{ ($item['price'] ?? 0) * ($item['qty'] ?? 1) }}" data-value="{{ ($item['price'] ?? 0) * ($item['qty'] ?? 1) }}" readonly required>
                    </div>
                    </div>
                @endforeach
            @else
                <div class="item-row">
                    <button type="button" class="delete-btn" onclick="deleteItemRow(this)">🗑️</button>
                    <input type="text" name="items[0][name]" class="item-input" placeholder="Name Item" required>
                    <div class="item-details">
                    <input type="number" name="items[0][price]" class="price-input" placeholder="Price" onchange="calculateTotal(this)" data-type="price" required>
                    <input type="number" name="items[0][qty]" class="qty-input" placeholder="Qty" onchange="calculateTotal(this)" data-type="qty" value="1" required>
                    <input type="number" name="items[0][total]" class="total-input" placeholder="Total Price" data-type="total" value="0" data-value="0" readonly required>
                    </div>
                </div>
            @endif
            
            <div class="add-item-btn" id="add-item-btn">
            <span>+</span>
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-title">SUMMARY</div>
            
            <div class="summary-row">
                <div class="summary-label">Subtotal</div>
                <div class="input-with-prefix">
                    <span class="prefix">Rp.</span>
                    <input type="number" name="subtotal" class="summary-value" id="subtotal-value" value="0" data-value="0">
                </div>
            </div>

            <div class="summary-row">
                <div class="summary-label">Tax</div>
                <div class="input-with-prefix">
                    <span class="prefix">Rp.</span>
                    <input type="number" name="tax" class="summary-value" id="tax-value" value="0" data-value="0" onchange="calculateTotalAmount()">
                </div>
            </div>

            <div class="summary-row">
                <div class="summary-label">Total amount</div>
                <div class="input-with-prefix">
                    <span class="prefix">Rp.</span>
                    <input type="number" name="total_amount" class="summary-value" id="total-amount-value" value="0" data-value="0">
                </div>
            </div>
        </div>
        
        <button type="submit" class="confirm-btn">CONFIRM</button>
    </div>
</form>

<script>
   
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('bill-items-container');
        const itemRows = container.querySelectorAll('.item-row');
        const newIndex = itemRows.length;

        const newRow = document.createElement('div');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <button type="button" class="delete-btn" onclick="deleteItemRow(this)">🗑️</button>
            <input type="text" name="items[${newIndex}][name]" class="item-input" placeholder="Name Item">
            <div class="item-details">
                <input type="number" name="items[${newIndex}][price]" class="price-input" placeholder="Price" onchange="calculateTotal(this)" data-type="price" required>
                <input type="number" name="items[${newIndex}][qty]" class="qty-input" placeholder="Qty" onchange="calculateTotal(this)" data-type="qty" value="1" required>
                <input type="number" name="items[${newIndex}][total]" class="total-input" placeholder="Total Price" data-type="total" readonly required>
            </div>
        `;

        container.insertBefore(newRow, this);
    });
   
    function deleteItemRow(button) {
        const row = button.closest('.item-row');
        
        
        const container = document.getElementById('bill-items-container');
        const itemRows = container.querySelectorAll('.item-row');
        
        if (itemRows.length > 1) {
            row.remove();
            calculateSubtotal();
        } else {
            
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.type !== 'button') {
                    input.value = input.getAttribute('data-type') === 'qty' ? '1' : '';
                }
            });
            
          
            const totalInput = row.querySelector('[data-type="total"]');
            totalInput.value = '';
            totalInput.setAttribute('data-value', 0);
            
            calculateSubtotal();
        }
    }
    
 
    function calculateTotal(input) {
        const row = input.closest('.item-row');
        const priceInput = row.querySelector('[data-type="price"]');
        const qtyInput = row.querySelector('[data-type="qty"]');
        const totalInput = row.querySelector('[data-type="total"]');
        
        const price = parseFloat(priceInput.value) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const total = price * qty;
        
        totalInput.value = total;
        totalInput.setAttribute('data-value', total);
        
        calculateSubtotal();
    }
    
    function calculateSubtotal() {
        const totalInputs = document.querySelectorAll('[data-type="total"]');
        let subtotal = 0;
        
        totalInputs.forEach(input => {
            subtotal += parseFloat(input.getAttribute('data-value')) || 0;
        });
        
        const subtotalInput = document.getElementById('subtotal-value');
        subtotalInput.value = subtotal;
        subtotalInput.setAttribute('data-value', subtotal);
        
        calculateTotalAmount();
    }
    
    function calculateTotalAmount() {
        const subtotalInput = document.getElementById('subtotal-value');
        const taxInput = document.getElementById('tax-value');
        const totalAmountInput = document.getElementById('total-amount-value');
        
        const subtotal = parseFloat(subtotalInput.getAttribute('data-value')) || 0;
        let tax;
        
        const taxValue = taxInput.value.trim();
        if (taxValue.includes('%')) {
            const percentage = parseFloat(taxValue.replace('%', '')) || 0;
            tax = (subtotal * percentage) / 100;
        } else {
            tax = parseFloat(taxValue.replace(/[^\d.-]/g, '')) || 0;
        }
        
        taxInput.setAttribute('data-value', tax);
        taxInput.value = tax;
        
        const totalAmount = subtotal + tax;
        totalAmountInput.value = totalAmount;
        totalAmountInput.setAttribute('data-value', totalAmount);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const taxInput = document.getElementById('tax-value');
    
        calculateSubtotal();
        
        taxInput.addEventListener('focus', function() {
            if (this.value === '0') {
                this.value = '';
            }
        });
        
        taxInput.addEventListener('blur', function() {
            if (this.value === '') {
                this.value = '0';
                this.setAttribute('data-value', 0);
            }
            calculateTotalAmount();
        });
        
        const subtotalInput = document.getElementById('subtotal-value');
        subtotalInput.addEventListener('focus', function() {
            if (this.value === '0') {
                this.value = '';
            }
        });
        
        subtotalInput.addEventListener('blur', function() {
            if (this.value === '') {
                this.value = '0';
                this.setAttribute('data-value', 0);
            } else {
                const value = parseFloat(this.value.replace(/[^\d.-]/g, '')) || 0;
                this.value = value;
                this.setAttribute('data-value', value);
                calculateTotalAmount();
            }
        });
        
        subtotalInput.readOnly = false;

        calculateSubtotal();
    });
</script>
@endsection
