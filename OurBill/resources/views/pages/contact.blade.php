@extends('layout')

@section('content')
<style> 
    .menu-icon {
        font-size: 24px;
        margin-right: 15px;
        cursor: pointer;
        color: #6b8e23;
    }
    
    .logo {
        font-weight: bold;
        font-size: 20px;
    }
    
    .icon-button {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        text-align: center;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;
        margin-left: 15px;
        background-color: #f0f0f0;
        color: #7d9d61;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 0;
        line-height: 1;
    }

    .icon-button:hover {
    background-color: #7d9d61;
    color: white;
    transition: 0.3s;
    }
    
    .add {
        height: 24px;
        width: 24px;
    }
    
    .search-container {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
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
    
    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 260px;
        height: 100%;
        background-color: #e6f4d5;
        padding: 20px;
        transition: 0.3s;
        z-index: 999; 
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .sidebar.active {
        left: 0;
    }
    
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        display: none;
        z-index: 998;
    }

    .overlay.active {
        display: block;
    }

    .profile-pic {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .edit-icon {
        width: 20px;
        height: 20px;
        vertical-align: middle;
        margin-left: 2px;
    }

    .profile-id {
        background-color: #c2e59c;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
        margin-top: 5px;
    }

    .divider {
        margin: 20px 0;
        border: 0;
        border-top: 1px solid #ccc;
    }

    .menu-list {
        list-style: none;
        padding: 0;
    }

    .menu-list li {
        margin: 15px 0;
    }

    .menu-list a {
        text-decoration: none;
        color: #6b8e23;
        font-weight: bold;
    }

    .signout-button {
        display: block;
        margin-top: 20px;
        color: #6b8e23;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
    }

    /* Contact list styles */
    .contact-list {
        width: 100%;
        padding: 0;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
    }

    .contact-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .contact-name {
        font-weight: 500;
        font-size: 16px;
        margin-left: 10px;
    }

    .more-options {
        position: absolute;
        right: 15px;
        cursor: pointer;
        font-size: 20px;
        color: #888;
    }
    .plus-icon {
    position: relative;
    top:-3px;
    }
</style>

<div id="sidebar" class="sidebar">
    <div class="profile-section">
        @if(auth()->user()->profile === null)
            <img src="pictures/moneylogo.png" alt="Profile" class="profile-pic">
        @else
            <img src="{{ 'storage/' . auth()->user()->profile }}" alt="Profile" class="profile-pic">
        @endif
        <h3>
            {{ auth()->user()->name }}
            <a href="edit-profile" title="Edit Profil">
                <img src="gambar/edit.1024x1024.png" alt="Edit" class="edit-icon">
            </a>
        </h3>
        <span class="profile-id">ID : {{ auth()->user()->username }}</span>
    </div>
    
    <hr class="divider">

    <ul class="menu-list">
        <li><a href="{{ route('dashboard') }}">HOME</a></li>
        <hr class="divider">
        <li><a href="{{ route('contact') }}">CONTACT</a></li>
        <hr class="divider">
        <li><a href="/groups">GROUP</a></li>
    </ul>

    <hr class="divider">

    <a href="{{ route('logout') }}" class="signout-button">SIGN OUT</a>
</div>

<div id="overlay" class="overlay" onclick="closeSidebar()"></div>

<div class="header">
    <div class="header-left">
        <div class="menu-icon" onclick="toggleSidebar()">☰</div>
        <div class="logo">CONTACT</div>
    </div>
    <div class="icon-button" onclick="window.location.href='/add-contact'">
        <span class="plus-icon">+</span>
    </div>
</div>

<div class="search-container">
    <div class="search-box">
        <div class="search-icon">
            <img class="kaca" src="{{ asset('gambar/searchlogo.png') }}" alt="search">
        </div>
        <input type="text" class="search-input" placeholder="Search Groups">
    </div>
</div>

<div class="contact-list">
    @foreach($contacts as $contact)
        <div class="contact-item">
            @php
                if($contact->User->username === auth()->user()->username) {
                    $friend = $contact->Friend;
                }
                else{
                    $friend = $contact->User;
                }
            @endphp
            @if($friend->profile === null)
                <img src="pictures/moneylogo.png" alt="Profile" class="profile-pic">
            @else
                <img src="{{ 'storage/' . $friend->profile }}" alt="Profile" class="profile-pic">
            @endif
            <div class="contact-name">{{ $friend->name }}</div>
            @if($contact->status === 'friend')
                <div class="more-options">⋮</div>
            @elseif($contact->status === 'their_request')
                <div class="more-options">
                    <form action="/contact/accept" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                        <button type="submit" class="btn btn-success">Accept</button>
                    </form>
                    <form action="/contact/reject" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            @elseif($contact->status === 'your_request')
                <div class="more-options">Pending</div>
            @endif
        </div>
    @endforeach
</div>

<script>
    function acceptRequest(contactId) {
        // Add logic to handle accepting the request
        console.log('Accepting request for contact ID:', contactId);
    }

    function rejectRequest(contactId) {
        // Add logic to handle rejecting the request
        console.log('Rejecting request for contact ID:', contactId);
    }
</script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    }
    
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    }
</script>
@endsection
