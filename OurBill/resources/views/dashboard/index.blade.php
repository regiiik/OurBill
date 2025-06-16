@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
@endsection

@section('content')

<style>
.kaca{
    height: 12px;
    width: 12px;
}

.add{
    height: 32px;
    width: 32px;
    cursor: pointer;
}

.camera{
    height: 32px;
    width: 32px;
    cursor: pointer;
}

.btn{
    border-radius: 15px;
    margin: 5px 0;
    padding: 12px 25px;
    width: 80%;
    max-width: 300px;
    text-align: center;
}

.tombol{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 85%;
    max-width: 300px;
    height: 100%;
    background-color: #e6f4d5;
    padding: 20px;
    transition: 0.3s;
    z-index: 999; 
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
    overflow-y: auto;
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

.profile-pic
{
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
    color: red;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
}
.menu-icon
{
    font-size: 24px;
    cursor: pointer;
    margin-right: 10px;
}
.profile-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        position: relative;
    }
    .profile-icon svg {
        width: 60px;
        height: 60px;
        color: #666;
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
    <div id="sidebar" class="sidebar">
        <h2>Profile</h2>
        <p>Nama: {{ auth()->user()->name }}</p>
        <p>Email: {{ auth()->user()->email }}</p>
        <button onclick="closeSidebar()" class="btn">Tutup</button>
    </div>
    <div id="overlay" class="overlay" onclick="closeSidebar()"></div>
    
    <div class="header">
        <div class="header-left">
            <div class="menu-icon" onclick="toggleSidebar()"><img src="img/burger.svg" alt="" width="40px" height="40px"></div>
            <div class="logo">HOME</div>
        </div>
        <div class="header-right">
            <div class="icon-button">
                <a href="/bill1"><img class="add" src="img/plus.svg" alt="add.png"></a>
            </div>
            <form action="/ocr" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="icon-button">
                    <label for="file-input">
                        <img class="camera" src="img/camera.svg" alt="Add Photo">
                    </label>
                    <input id="file-input" type="file" name="file" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>
    
    <div class="search-container">
        <div class="search-box">
            <div class="search-icon">
                <img class="kaca" src="img/search.svg" alt="Search">
            </div>
            <input type="text" class="search-input" placeholder="Search Contacts / Groups">
        </div>
    </div>
    @if(session('success'))
        <div id="success-alert" class="alert alert-success" style="color: green; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(count($bills) > 0)
        <div class="recent-heading">RECENT CONTACTS / GROUPS</div>

        <div class="contact-list">
            @php
                $bills = $bills->sortByDesc('created_at');
            @endphp
            @foreach ($bills as $bill)
            @php
                $bill = $bill->bill;
                $span = $bill->user->count() - 3;
                if($span < 0) {
                    $span = 0;
                }
                $userCount = $bill->user->count();
                if($userCount > 3){
                    $userCount = 3;
                }
            @endphp
            <div class="contact-card" onclick="location.href='/totalamount/{{ $bill->id }}'" style="cursor: pointer;">
                <div class="contact-header">
                    <div class="contact-name text-white">{{ $bill->name }}</div>
                    <div class="contact-avatars" style="display: flex; align-items: center;">
                        @for($index = 0; $index < $userCount; $index++)
                            @if($bill->user[$index]->profile === null)
                                <img class="contact-avatar" src="pictures/moneylogo.png" alt="avatar">
                            @else
                                <img class="contact-avatar" src="{{ 'storage/' . $bill->user[$index]->profile }}" alt="avatar">
                            @endif
                        @endfor
                        @if($span > 0)
                            <span class="contact-count">+{{ $span }}</span>
                        @endif
                    </div>
                </div>
                <div class="bill-amount text-white">Bill Amount</div>
                <div class="bill-value text-white">{{ $bill->total_amount }}</div>
                @if($bill->status == 'undone')
                    <span class="status-badge status-undone">UNDONE</span>
                @else
                    <span class="status-badge status-done">✓ DONE</span>
                @endif
            </div>
            @endforeach

        </div>
    @else
        <div class="content">
            <div class="empty-state">
                <div class="profile-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h2 class="empty-title">No Contacts or Groups Yet</h2>
                <p class="empty-description">Start by creating a new group to split bills with your friends!</p>
        
                <div class="tombol">
                    <a href="{{ route('add-contact') }}" class="btn">ADD CONTACT</a>
                    <a href="/groups" class="btn">CREATE GROUP</a>
                </div>
            </div>
        </div>
    @endif

    
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-input');
        const cards = Array.from(document.querySelectorAll('.contact-card'));

        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();

            cards.forEach(card => {
                const nameEl = card.querySelector('.contact-name');
                const name = nameEl ? nameEl.textContent.trim().toLowerCase() : '';

                if (name.includes(q)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    setTimeout(function() {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000);
</script>
@endsection
