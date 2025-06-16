@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
@endsection

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
    .icon-button:hover {
    background-color: #7d9d61;
    color: white;
    transition: 0.3s;
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
    
    .group-list {
        width: 100%;
        padding: 0;
        max-width: 800px;
        margin: 0 auto;
    }

    .group-item {
        padding: 16px;
        border-bottom: 1px solid #e0e0e0;
        position: relative;
    }

    .group-header {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }

    .group-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .group-name {
        font-size: 16px;
        font-weight: bold;
    }

    .more-options {
        position: absolute;
        right: 15px;
        top: 25px;
        cursor: pointer;
        font-size: 20px;
        color: #888;
    }

    .group-members {
        display: flex;
        margin-left: 65px;
        flex-wrap: wrap;
    }

    .member-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: -8px;
        border: 2px solid white;
    }

    .content-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 20px;
        max-width: 400px;
        margin: 0 auto;
    }

    .create-group-btn {
        background-color: #7d9d61;
        color: white;
        border: none;
        padding: 10px 40px;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .plus-icon {
    position: relative;
    top:-3px;
    }

    @media (max-width: 480px) {
        .group-avatar {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        
        .member-avatar {
            width: 25px;
            height: 25px;
        }
        
        .group-members {
            margin-left: 50px;
        }
    }

    @media (min-width: 768px) {
        .group-item {
            padding: 20px;
            transition: background-color 0.2s;
        }
        
        .group-item:hover {
            background-color: #f9f9f9;
        }
        
        .group-avatar {
            width: 60px;
            height: 60px;
        }
        
        .group-name {
            font-size: 18px;
        }
        
        .member-avatar {
            width: 35px;
            height: 35px;
        }
    }
</style>
<div class="container">
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
            <div class="menu-icon" onclick="toggleSidebar()"><img src="img/burger.svg" alt="" width="40px" height="40px"></div>
            <div class="logo">GROUPS</div>
        </div>
        <div class="icon-button" onclick="window.location.href='/create-group'">
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
    
    @if($groups->isEmpty())
        <div class="content-container">
            <div class="empty-state">
                <div class="profile-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h2 class="empty-title">No Contacts or Group Yet</h2>
                <p class="empty-description">Start by creating a new group to split bills with your friends!</p>
                <a href="/create-group" class="create-group-btn">CREATE GROUP</a>
            </div>
        </div>
    @else
        <div class="group-list">
        @foreach($groups as $group)
            <div class="group-item">
            <div class="group-header">
                <img
                src="{{ $group->creator->profile
                            ? asset('storage/'.$group->creator->profile)
                            : asset('pictures/moneylogo.png') }}"
                alt="{{ $group->name }}"
                class="group-avatar"
                >

                <div class="group-name">{{ $group->name }}</div>
                <div class="more-options">⋮</div>
            </div>

            <div class="group-members flex items-center space-x-2">
                <img
                    src="{{ $group->creator->profile
                            ? asset('storage/'.$group->creator->profile)
                            : asset('pictures/moneylogo.png') }}"
                    alt="{{ $group->creator->username }}"
                    title="{{ $group->creator->name }}"
                    class="member-avatar"
                    style="border: 2px solid red;"
                >

                @foreach($group->members->where('id', '!=', $group->creator->id) as $member)
                    <img
                    src="{{ $member->profile
                                ? asset('storage/'.$member->profile)
                                : asset('pictures/moneylogo.png') }}"
                    alt="{{ $member->username }}"
                    title="{{ $member->name }}"
                    class="member-avatar rounded-full border border-gray-300"
                    >
                @endforeach
            </div>

            </div>
        @endforeach
        </div>
    @endif


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
