@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
@endsection

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
    
    .user-section {
        padding: 0 20px;
        margin-bottom: 20px;
    }
    
    .user-label {
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .user-card {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .search-container {
        padding: 0 20px;
        margin-bottom: 15px;
    }
    
    .search-box {
        display: flex;
        align-items: center;
        background-color: #f5f5f5;
        border-radius: 20px;
        padding: 8px 15px;
    }
    
    .search-icon {
        margin-right: 10px;
    }
    
    .kaca {
        height: 12px;
        width: 12px;
    }
    
    .search-input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
        font-size: 14px;
    }
    
    .section-title {
        font-weight: bold;
        margin-bottom: 15px;
        padding: 0 20px;
    }
    
    .contact-list {
        list-style: none;
        padding: 0 20px;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .contact-info {
        display: flex;
        align-items: center;
    }
    
    .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
    }
    
    .contact-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .contact-name {
        font-weight: 500;
    }
    
    .checkbox {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .checkbox.checked {
        background-color: #6b8e23;
        border-color: #6b8e23;
        color: white;
    }
    
    .next-btn {
        text-decoration: none;
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #7d9d61;
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        max-width: 200px;
        width: 100%;
    }
    
    .divider {
        height: 1px;
        background-color: #f0f0f0;
        margin: 15px 20px;
    }
</style>
    
    <div class="container">
        <div class="page-header">
            <a href="/bill1" class="back-arrow">←</a>
            <div class="page-title">Our Bill</div>
            <div class="page-subtitle">SPLIT THE BILL</div>
            <div class="page-description">You can add contact or group to join the bill with you</div>
        </div>
        
        <div class="user-section">
            <div class="user-card">
                <div class="user-avatar">
                    <img src="{{ asset('pictures/moneylogo.png') }}" alt="You">
                </div>
                <span class="user-label">You</span>
            </div>
        </div>
        
        <div class="search-container">
            <div class="search-box">
                <div class="search-icon">
                    <img class="kaca" src="{{ asset('gambar/searchlogo.png') }}" alt="search">
                </div>
                <input type="text" class="search-input" placeholder="Search Contacts / Groups">
            </div>
        </div>

<form action="{{ route('bill3') }}" method="POST" onsubmit="return validateSelection()">
    @csrf
        <div class="section-title">Groups</div>
        <ul class="contact-list">
            @if($groups->isEmpty())
                <li class="contact-item">
                    <div class="contact-info">
                        <span class="contact-name">No groups available</span>
                    </div>
                </li>
            @else
                @foreach($groups as $group)
                    <li class="contact-item">
                        <div class="contact-info">
                            <div class="contact-avatar">
                                <img src="{{ $group->creator->profile 
                                            ? asset('storage/' . $group->creator->profile) 
                                            : asset('pictures/moneylogo.png') }}" 
                                    alt="{{ $group->name }}">
                            </div>
                            <span class="contact-name">{{ $group->name }}</span>
                        </div>
                        <div class="checkbox" data-type="group">
                            <input type="checkbox" name="groups[]" value="{{ $group->id }}" class="checkbox" style="display: none;">
                            <span class="checkmark text-white text-xl"></span>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
        
        <div class="divider"></div>
        
        <div class="section-title">Contacts</div>
        <ul class="contact-list">
            @if($contacts->isEmpty())
                <li class="contact-item">
                    <div class="contact-info">
                        <span class="contact-name">No contacts available</span>
                    </div>
                </li>
            @else
                @foreach($contacts as $contact)
                    @if($contact->status === 'friend')
                        @php
                            if($contact->User->username === auth()->user()->username) {
                                $friend = $contact->Friend;
                            }
                            else{
                                $friend = $contact->User;
                            }
                        @endphp
                        <li class="contact-item">
                            <div class="contact-info">
                                <div class="contact-avatar">
                                    @if($friend->profile === null)
                                        <img src="{{ asset('pictures/moneylogo.png') }}" alt="{{ $friend->name }}">
                                    @else
                                        <img src="{{ asset('storage/' . $friend->profile) }}" alt="{{ $friend->name }}">
                                    @endif
                                </div>
                                <span class="contact-name">{{ $friend->name }}</span>
                            </div>
                            <div class="checkbox" data-type="friend">
                                <input type="checkbox" name="friends[]" value="{{ $friend->id }}" class="checkbox" style="display: none;">
                                <span class="checkmark text-white text-xl"></span>
                            </div>
                        </li>
                    @else
                        <li class="contact-item">
                            <div class="contact-info">
                                <span class="contact-name">No friends available</span>
                            </div>
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
        @foreach ($items as $index => $item)
            <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item['name'] }}">
            <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item['price'] }}">
            <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}">
            <input type="hidden" name="items[{{ $index }}][total]" value="{{ $item['total'] }}">
        @endforeach

        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
        <input type="hidden" name="tax" value="{{ $tax }}">
        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">

        <div id="error-message" style="color: red; text-align: center; margin-bottom: 15px; display: none;">
            Please select at least one group or one friend.
        </div>

        <button type="submit" class="next-btn">NEXT</button>
    </div>
</form>

<script>
    document.querySelectorAll('.checkbox').forEach(container => {
        container.addEventListener('click', function() {
            const input = this.querySelector('input.checkbox');
            const span  = this.querySelector('.checkmark');

            if (input.checked) {
            input.checked = false;
            span.innerHTML = '';
            this.classList.remove('checked');
            } else {
            input.checked = true;
            span.innerHTML = '✓';
            this.classList.add('checked');
            }
        });
    });


    function validateSelection() {
        const selectedGroups = document.querySelectorAll('input[name="groups[]"]:checked').length;
        const selectedFriends = document.querySelectorAll('input[name="friends[]"]:checked').length;

        const errorMessage = document.getElementById('error-message');
        if (selectedGroups === 0 && selectedFriends === 0) {
            errorMessage.style.display = 'block';
            return false;
        }
        errorMessage.style.display = 'none';
        return true;
    }
</script>
</script>
@endsection
