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
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        position: relative;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .page-header h1 {
        font-weight: bold;
        font-size: 20px;
        margin: 0;
    }
    
    .back-btn {
        position: absolute;
        left: 15px;
    }
    
    .group-info {
        padding: 20px;
        text-align: center;
    }
    
    .info-text {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }
    
    .group-name-container {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 25px;
        padding: 8px 15px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .group-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    .group-name-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 14px;
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
    
    .contacts-section {
        padding: 0 20px;
    }
    
    .section-title {
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .contact-list {
        list-style: none;
        padding: 0;
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
        background-color: #f0f0f0;
        overflow: hidden;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
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
    
    .create-btn {
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
</style>
<div class="container">
    <div class="page-header">
        <div class="back-btn">
            <a href="/groups" class="back-arrow">←</a>
        </div>
        <h1>ADD GROUP</h1>
    </div>
    
    <div class="group-info">
        <p class="info-text">You can invite contact to make groups on your own</p>
        
        <div class="group-name-container">
            <div class="group-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" width="18" height="18">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
            <input type="text" class="group-name-input" name="group_name" placeholder="Group name (optional)">
        </div>
    </div>
    
    <div class="search-container">
        <div class="search-box">
            <div class="search-icon">
                <img class="kaca" src="{{ asset('gambar/searchlogo.png') }}" alt="search">
            </div>
            <input type="text" class="search-input" placeholder="Search Contacts">
        </div>
    </div>
    
    <form action="/create-group" method="POST">
        @csrf
        <div class="contacts-section">
            <h2 class="section-title">Contacts</h2>
            
            <ul class="contact-list">
                @foreach($contacts as $contact)
                    @if($contact->status === 'friend')
                        @php
                            if($contact->User->username === auth()->user()->username) {
                                $friend = $contact->Friend;
                            } else {
                                $friend = $contact->User;
                            }
                        @endphp
                        <li class="contact-item">
                            <div class="contact-info">
                                <div class="contact-avatar">
                                    @if($friend->profile === null)
                                        <img src="{{ asset('pictures/moneylogo.png') }}" alt="{{ $friend->name }}">
                                    @else
                                        <img src="{{ asset("storage/{$friend->profile}") }}" alt="{{ $friend->name }}">
                                    @endif
                                </div>
                                <span class="contact-name">{{ $friend->name }}</span>
                            </div>
                            <div class="checkbox" data-id="{{ $friend->id }}"></div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        
        <input type="hidden" name="group_name" id="group_name" value="">
        <input type="hidden" id="friend_ids">
        <button type="submit" class="create-btn">CREATE</button>
    </form>

    <script>

    </script>
</div>

<script>
    const selectedFriends = new Set();

    document.querySelectorAll('.checkbox').forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            this.classList.toggle('checked');
            const friendId = this.getAttribute('data-id');
            const friendIdsInput = document.getElementById('friend_ids');

            if (this.classList.contains('checked')) {
                selectedFriends.add(friendId);
                this.innerHTML = '✓';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'friend_ids[]';
                input.value = friendId;
                input.setAttribute('data-id', friendId);
                friendIdsInput.parentNode.insertBefore(input, friendIdsInput);
            } else {
                selectedFriends.delete(friendId);
                this.innerHTML = '';
                const inputToRemove = document.querySelector(`input[name="friend_ids[]"][value="${friendId}"]`);
                if (inputToRemove) {
                    inputToRemove.remove();
                }
            }
        });
    });

    const groupNameInput = document.querySelector('.group-name-input');
    groupNameInput.addEventListener('input', function() {
        document.getElementById('group_name').value = this.value;
    });
</script>
@endsection
