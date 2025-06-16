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
    
    .bill-container {
        padding: 0 20px;
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .group-title {
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .bill-item {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .item-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .item-details {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
        gap: 10px;
    }
    
    .price-section, .qty-section, .total-section {
        display: flex;
        flex-direction: column;
        min-width: 80px;
    }
    
    .detail-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }
    
    .detail-input {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 70px;
        text-align: center;
    }
    
    .user-avatars {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-around;
        margin-top: 10px;
    }
    
    .avatar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 40px;
    }
    
    .avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 5px;
        border: 2px solid transparent;
    }
    
    .avatar.selected {
        border-color: #6b8e23;
    }
    
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-name {
        font-size: 10px;
        text-align: center;
    }
    
    .treat-section {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    
    .treat-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 15px;
    }
    
    .percentage-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .percentage-symbol {
        font-size: 20px;
        font-weight: bold;
        margin-right: 10px;
    }
    
    .percentage-input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 120px;
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
    
    @media (max-width: 480px) {
        .item-details {
            justify-content: center;
        }
        
        .avatar {
            width: 30px;
            height: 30px;
        }
        
        .avatar-name {
            font-size: 9px;
        }
        
        .detail-input {
            width: 60px;
        }
    }
    
    @media (min-width: 768px) {
        .bill-item {
            padding: 20px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
        }
        
        .detail-input {
            width: 90px;
        }
    }
</style>
<div class="container">
    <div class="page-header">
        <a href="/bill2" class="back-arrow">←</a>
        <div class="page-title">Our Bill</div>
        <div class="page-subtitle">SPLIT THE BILL</div>
        <div class="page-description">You can assign the items by tapping a contact profile</div>
    </div>
    
    <form action="/create-bill" method="POST">
        @csrf
        <div class="bill-container">
            <div class="group-title">{{ Auth()->user()->name }}'s Bill</div>
            @foreach ($items as $index => $item)
                <div class="bill-item">
                    <input type="text" class="item-input" placeholder="Name Item" value="{{ $item['name'] }}" name="items[{{ $index }}][name]">
                    
                    <div class="item-details">
                        <div class="price-section">
                            <div class="detail-label">Price</div>
                            <input type="text" class="detail-input" value="{{ $item['price'] }}" name="items[{{ $index }}][price]">
                        </div>
                        
                        <div class="qty-section">
                            <div class="detail-label">qty.</div>
                            <input type="text" class="detail-input" value="{{ $item['qty'] }}" name="items[{{ $index }}][qty]">
                        </div>
                        
                        <div class="total-section">
                            <div class="detail-label">Total Price</div>
                            <input type="text" class="detail-input" value="{{ $item['total'] }}" name="items[{{ $index }}][total]">
                        </div>
                    </div>
                        
                    <div class="user-avatars">
                        @foreach ($billUsers as $user)
                            <div class="avatar-container" data-user-id="{{ $user->id }}">
                                <div class="avatar">
                                    <img src="{{ asset('storage/' . ($user->profile ?? 'default.png')) }}" alt="{{ $user->name }}">
                                </div>
                                <div class="avatar-name">{{ $user->name }}</div>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="items[{{ $index }}][users]" class="selected-users" value="">
                </div>
            @endforeach
            
            <div class="treat-section">
                <div class="treat-title">TREAT YOUR FRIENDS?</div>
                <div class="percentage-container">
                    <div class="percentage-symbol">%</div>
                    <input type="text" class="percentage-input" placeholder="Optional" name="treat_percentage">
                </div>
                
                <div class="user-avatars">
                    @foreach ($billUsers as $user)
                        <div class="avatar-container" data-user-id="{{ $user->id }}">
                            <div class="avatar">
                                <img src="{{ asset('storage/' . ($user->profile ?? 'default.png')) }}" alt="{{ $user->name }}">
                            </div>
                            <div class="avatar-name">{{ $user->name }}</div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="treat_users" id="treat-users" value="">
            </div>
        </div>

        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
        <input type="hidden" name="tax" value="{{ $tax }}">
        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
        <div class="error-message" style="color: red; font-weight: bold; display: none; margin-bottom: 20px;"></div>

        <button type="submit" class="confirm-btn">CONFIRM</button>
    </form>
    </div>
</div>


<script>
    document.querySelectorAll('.bill-item').forEach(billItem => {
    const selectedUsersInput = billItem.querySelector('.selected-users');
    const avatars = billItem.querySelectorAll('.avatar-container');

    avatars.forEach(avatar => {
        avatar.addEventListener('click', function() {
            this.querySelector('.avatar').classList.toggle('selected');
            updateSelectedUsers();
        });
    });

    function updateSelectedUsers() {
        const selectedUserIds = Array.from(avatars)
            .filter(avatar => avatar.querySelector('.avatar').classList.contains('selected'))
            .map(avatar => avatar.getAttribute('data-user-id'));

        selectedUsersInput.value = selectedUserIds.join(',');
    }
    });

        document.addEventListener('DOMContentLoaded', function() {
        const treatAvatars = document.querySelectorAll('.treat-section .avatar-container');
        const treatUsersInput = document.getElementById('treat-users');

        treatAvatars.forEach(avatar => {
            avatar.addEventListener('click', function() {
                this.querySelector('.avatar').classList.toggle('selected');
                updateTreatUsers();
            });
        });

        function updateTreatUsers() {
            const selectedTreatUserIds = Array.from(treatAvatars)
                .filter(avatar => avatar.querySelector('.avatar').classList.contains('selected'))
                .map(avatar => avatar.getAttribute('data-user-id'));

            treatUsersInput.value = selectedTreatUserIds.join(',');
        }
    });
    
    document.querySelector('form').addEventListener('submit', function(event) {
        let isValid = true;
        const errorMessageDiv = document.querySelector('.error-message');
        errorMessageDiv.style.display = 'none';
        errorMessageDiv.textContent = '';

        document.querySelectorAll('.bill-item').forEach(billItem => {
            const selectedUsersInput = billItem.querySelector('.selected-users');
            if (!selectedUsersInput.value) {
                isValid = false;
                errorMessageDiv.textContent = 'Please select at least one user for each item.';
                errorMessageDiv.style.display = 'block';
                return false;
            }
        });

        if (!isValid) {
            event.preventDefault();
        }
    });

    document.querySelector('form').addEventListener('submit', function(event) {
    let isValid = true;
    const errorMessageDiv = document.querySelector('.error-message');
    errorMessageDiv.style.display = 'none';
    errorMessageDiv.textContent = '';
    document.querySelectorAll('.bill-item').forEach(billItem => {
        const selectedUsersInput = billItem.querySelector('.selected-users');
        if (!selectedUsersInput.value) {
            isValid = false;
            errorMessageDiv.textContent = 'Please select at least one user for each item.';
            errorMessageDiv.style.display = 'block';
            return false;
        }
    });

    const treatPercentageInput = document.querySelector('.percentage-input');
    const treatUsersInput = document.getElementById('treat-users');
    if (treatPercentageInput.value && !treatUsersInput.value) {
            isValid = false;
            errorMessageDiv.textContent = 'Please select at least one user for the treat.';
            errorMessageDiv.style.display = 'block';
        }

    if (!isValid) {
            event.preventDefault();
        }
    });
</script>
@endsection
