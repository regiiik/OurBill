@extends('layout')

@section('content')

<style>
    .kaca {
        height: 12px;
        width: 12px;
    }

    .add {
        height: 32px;
        width: 32px;
    }

    .camera {
        height: 32px;
        width: 32px;
    }

    .btn {
        border-radius: 15px;
        margin-top: 10px;
        margin-bottom: 10px;
        background-color: #6b8e23;
        color: white;
        border: none;
        padding: 8px 25px;
    }

    .back-btn {
        text-decoration: none;
        color: black;
        font-weight: bold;
        margin-right: 10px;
    }

    .contact-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }

    .contact-header h1 {
        font-weight: bold;
        font-size: 22px;
        margin: 0;
    }

    .back-arrow {
        position: absolute;
        left: 15px;
    }

    .contact-container {
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .contact-subtitle {
        text-align: center;
        color: #444;
        margin-bottom: 20px;
    }

    .search-box {
        width: 100%;
        max-width: 450px;
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 15px;
        margin-bottom: 30px;
    }

    .search-input {
        border: none;
        flex-grow: 1;
        outline: none;
        margin-left: 10px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        background-color: #eee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .contact-name {
        font-weight: bold;
        margin-bottom: 20px;
    }

    .add-contact-btn {
        width: 100%;
        max-width: 300px;
    }

    .suggested-contacts {
        width: 100%;
        max-width: 500px;
        margin-top: 30px;
    }

    .suggested-title {
        font-weight: bold;
        margin-bottom: 15px;
        padding-left: 10px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .contact-info {
        display: flex;
        align-items: center;
    }

    .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #eee;
        overflow: hidden;
        margin-right: 15px;
    }

    .contact-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .contact-add-btn {
        background-color: #6b8e23;
        color: white;
        border: none;
        border-radius: 15px;
        padding: 5px 20px;
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

    @media (min-width: 768px) {
        .contact-item {
            padding: 15px;
        }
        
        .contact-avatar {
            width: 50px;
            height: 50px;
        }
        
        .contact-add-btn {
            padding: 8px 25px;
        }
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

<div class="contact-header">
    <div class="back-arrow">
        <a href="/home" class="back-btn">←</a>
    </div>
    <h1>ADD CONTACT</h1>
</div>

<div class="contact-container">
    <p class="contact-subtitle">You can add people's contact from profile ID</p>
    
    <form action="/search-contact" method="POST" class="search-box">
        @csrf
        <img class="kaca" src="{{ asset('gambar/searchlogo.png') }}" alt="Search">
        <input type="text" name="username" class="search-input" placeholder="Search ID">
        <button type="submit" style="display: none;"></button>
    </form>
    
    @if(isset($user))

        @if($user->profile === null)
            <div class="profile-avatar">
                <img src="pictures/moneylogo.png" alt="Profile">
            </div>
        @else
            <div class="profile-avatar">
                <img src="{{ 'storage/' . $user->profile }}" alt="User">
            </div>
        @endif
        
        <p class="contact-name">{{ $user->name }}</p>
        
        <form action="/add-contact" method="POST">
            @csrf
            <input type="hidden" name="username" value="{{ $user->username }}">
            <button class="btn add-contact-btn" type="submit">ADD</button>
        </form>
    @elseif(isset($error))
        <p class="contact-subtitle" style="color: red;">{{ $error }}</p>
    @endif
    
    <div class="suggested-contacts">
        <p class="suggested-title">Suggested Contacts</p>
        
        <div class="contact-item">
            <div class="contact-info">
                <div class="contact-avatar">
                    <img src="{{ asset('pictures/moneylogo.png') }}" alt="Contact E">
                </div>
                <span>E</span>
            </div>
            <button class="contact-add-btn">ADD</button>
        </div>
        
        <div class="contact-item">
            <div class="contact-info">
                <div class="contact-avatar">
                    <img src="{{ asset('pictures/moneylogo.png') }}" alt="Contact D">
                </div>
                <span>D</span>
            </div>
            <button class="contact-add-btn">ADD</button>
        </div>
        
        <div class="contact-item">
            <div class="contact-info">
                <div class="contact-avatar">
                    <img src="{{ asset('pictures/moneylogo.png') }}" alt="Contact C">
                </div>
                <span>C</span>
            </div>
            <button class="contact-add-btn">ADD</button>
        </div>
        
        <div class="contact-item">
            <div class="contact-info">
                <div class="contact-avatar">
                    <img src="{{ asset('pictures/moneylogo.png') }}" alt="Contact B">
                </div>
                <span>B</span>
            </div>
            <button class="contact-add-btn">ADD</button>
        </div>
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

