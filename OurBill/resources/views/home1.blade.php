@extends('layout')

@section('content')

<style>
.kaca{
    height: 12px;
    width: 12px;
}

.add{
    height: 32px;
    width: 32px;
}

.camera{
    height: 32px;
    width: 32px;
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

.menu-icon {
    font-size: 24px;
    cursor: pointer;
    margin-right: 10px;
    color: #6b8e23;
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

.search-input {
    border: none;
    background: transparent;
    width: 100%;
    outline: none;
    font-size: 14px;
}

/* Styles for contact/group cards */
.recent-heading {
    padding: 0 20px;
    margin-top: 20px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: bold;
    color: #333;
}

.contact-card {
    background-color: #7a996b;
    border-radius: 15px;
    margin: 0 20px 15px 20px;
    padding: 15px;
    color: white;
}

.contact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.contact-name {
    font-weight: bold;
    font-size: 18px;
}

.contact-avatars {
    display: flex;
    align-items: center;
}

.contact-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-left: -10px;
    border: 2px solid #7a996b;
}

.contact-avatar:first-child {
    margin-left: 0;
}

.contact-count {
    background-color: white;
    color: #333;
    border-radius: 50%;
    padding: 2px 5px;
    font-size: 12px;
    margin-left: 5px;
}

.bill-amount {
    font-size: 12px;
    margin-bottom: 5px;
}

.bill-value {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
}

.status-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.status-done {
    background-color: #e6f4d5;
    color: #6b8e23;
}

.status-undone {
    background-color: #f9d7d7;
    color: #e74c3c;
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

.divider {
    margin: 20px 0;
    border: 0;
    border-top: 1px solid #ccc;
}

.profile-pic {
    width: 70px;
    height: 70px;
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

.signout-button {
    display: block;
    margin-top: 20px;
    color: #6b8e23;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
}
</style>

<div id="sidebar" class="sidebar">
    <div class="profile-section">
        <img src="{{ asset('pictures/moneylogo.png') }}" alt="Profile" class="profile-pic">
        <h3>
            Abang Ganteng
            <a href="/editprofile" title="Edit Profil">
              <img src="{{ asset('gambar/edit.1024x1024.png') }}" alt="Edit" class="edit-icon">
            </a>
        </h3>
        <span class="profile-id">ID : ganteng123</span>
    </div>
    
    <hr class="divider">

    <ul class="menu-list">
        <li><a href="/home">HOME</a></li>
        <hr class="divider">
        <li><a href="/contactdefault">CONTACT</a></li>
        <hr class="divider">
        <li><a href="/groups">GROUP</a></li>
        <hr class="divider">
        <li><a href="/home1">HOME1</a></li>
    </ul>

    <hr class="divider">

    <a href="/" class="signout-button">SIGN OUT</a>
</div>

<div id="overlay" class="overlay" onclick="closeSidebar()"></div>

<div class="header">
    <div class="header-left">
        <div class="menu-icon" onclick="toggleSidebar()">☰</div>
        <div class="logo">HOME</div>
    </div>
    <div class="header-right">
        <div class="icon-button">
            <img class="add" src="{{ asset('gambar/add.png') }}" alt="add">
        </div>
        <div class="icon-button">
            <img class="camera" src="{{ asset('gambar/camera.png') }}" alt="camera">
        </div>
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

<div class="recent-heading">RECENT CONTACTS / GROUPS</div>

<div class="contact-list">
    
    <div class="contact-card">
        <div class="contact-header">
            <div class="contact-name">GULTIK</div>
            <div class="contact-avatars">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
            </div>
        </div>
        <div class="bill-amount">Bill Amount</div>
        <div class="bill-value">Rp. 55.000</div>
        <span class="status-badge status-done">✓ DONE</span>
    </div>
    
    <div class="contact-card">
        <div class="contact-header">
            <div class="contact-name">WARCEH</div>
            <div class="contact-avatars">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <span class="contact-count">+1</span>
            </div>
        </div>
        <div class="bill-amount">Bill Amount</div>
        <div class="bill-value">Rp. 121.899</div>
        <span class="status-badge status-undone">UNDONE</span>
    </div>
    
    <div class="contact-card">
        <div class="contact-header">
            <div class="contact-name">MARIMAS</div>
            <div class="contact-avatars">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <span class="contact-count">+2</span>
            </div>
        </div>
        <div class="bill-amount">Bill Amount</div>
        <div class="bill-value">Rp. 28.000</div>
        <span class="status-badge status-done">✓ DONE</span>
    </div>

    <!-- Additional contacts can be added here -->
    <div class="contact-card">
        <div class="contact-header">
            <div class="contact-name">BAKSO</div>
            <div class="contact-avatars">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
            </div>
        </div>
        <div class="bill-amount">Bill Amount</div>
        <div class="bill-value">Rp. 75.000</div>
        <span class="status-badge status-done">✓ DONE</span>
    </div>
    
    <div class="contact-card">
        <div class="contact-header">
            <div class="contact-name">MARTABAK</div>
            <div class="contact-avatars">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <img class="contact-avatar" src="{{ asset('pictures/moneylogo.png') }}" alt="avatar">
                <span class="contact-count">+3</span>
            </div>
        </div>
        <div class="bill-amount">Bill Amount</div>
        <div class="bill-value">Rp. 150.000</div>
        <span class="status-badge status-undone">UNDONE</span>
    </div>
</div>

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
