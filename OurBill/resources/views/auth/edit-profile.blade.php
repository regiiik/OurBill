@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
    <meta name="_token" content="{{ csrf_token() }}">
@endsection

@section('content')

<style>
    .back-btn {
        text-decoration: none;
        color:rgb(0, 0, 0);
        font-size: 20px;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        position: relative;
        text-align: center;
    }
    
    .header-title {
        font-weight: bold;
        font-size: 18px;
    }
    
    .profile-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }
    
    .profile-pic-container {
        position: relative;
        margin-bottom: 30px;
    }
    
    .profile-pic {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .camera-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: white;
        border-radius: 50%;
        padding: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .camera-icon img {
        width: 20px;
        height: 20px;
    }
    
    .form-container {
        width: 100%;
        max-width: 350px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        color: #6b8e23;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .form-group input {
        width: 100%;
        padding: 10px 0;
        border: none;
        border-bottom: 1px solid #ccc;
        font-size: 14px;
        outline: none;
    }
    
    .form-hint {
        font-size: 12px;
        color: #999;
        font-style: italic;
        margin-top: 5px;
    }
    
    .required-field label::before {
        content: '*';
        color: #6b8e23;
        margin-right: 2px;
    }
    
    .forgot-password {
        text-align: center;
        margin: 20px 0;
    }
    
    .forgot-password a {
        color: #6b8e23;
        font-size: 12px;
        text-decoration: none;
        font-weight: bold;
    }
    
    .save-btn {
        background-color: #6b8e23;
        color: white;
        border: none;
        border-radius: 15px;
        padding: 12px;
        width: 100%;
        font-size: 14px;
        text-transform: uppercase;
        cursor: pointer;
        font-weight: bold;
        margin-top: 20px;
    }
    .status-icon {
        position: absolute;
        right: 0.75rem;
        bottom: 0.75rem;
        width: 1.2em;
        height: 1.2em;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 100;
        }
    .status-icon.visible {
        opacity: 1;
    }
</style>
<div class="container">
    <div class="header">
        <a href="{{ route('dashboard') }}" class="back-btn">←</a>
        <div class="header-title">EDIT PROFILE</div>
    </div>
    
    <div class="profile-container">
        <div class="profile-pic-container" onclick="triggerFileInput()">
            @if(auth()->user()->profile)
                <img src="{{'storage/' . auth()->user()->profile }}" alt="Profile" id="profile_preview" class="profile-pic">
            @else
                <img src="pictures/moneylogo.png" alt="Profile" id="profile_preview" class="profile-pic">
            @endif
            <div class="camera-icon">
                <img src="gambar/camera.png" alt="Change Photo">
            </div>
        </div>
        
        <div class="form-container">
            <form action="{{ route('edit-profile.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <input type="file" accept="image/*" name="profile_picture" id="profile_picture" style="display: none;">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}">
                </div>
                
                <div class="mb-3 form-group position-relative">
                    <input
                      type="text"
                      name="username"
                      id="username"
                      class="form-control border-0 border-bottom poppins-regular"
                      placeholder="Username / ID"
                      required
                      value="{{ $user->username }}"
                      autocomplete="off"
                    >
                    <img
                      id="username-status-icon"
                      src="img/cross.svg"
                      alt=""
                      class="status-icon z-10"
                      hidden
                    >
                </div>
                
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}">
                </div>
                @if(auth()->user()->provider !== 'google')
                    <div class="form-group required-field">
                        <label for="password">Confirm Password</label>
                        <input type="password" id="password" name="password" required>
                        <div class="form-hint">must fill password to verify</div>
                    </div>
                @endif
                <div class="forgot-password">
                    <a href="{{ route('forgot-password') }}">FORGOT PASSWORD?</a>
                </div>
                
                <button type="submit"
                id="save-btn"
                class="btn save-btn w-100 mb-4 poppins-medium">
                    SAVE
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function triggerFileInput() {
        document.getElementById('profile_picture').click();
    }
    
    document.getElementById('profile_picture').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById('profile_preview').src = e.target.result;
            };

            reader.readAsDataURL(file);
        }
    });
    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
        };
    }
    const usernameInput = document.getElementById('username');
    const statusIcon    = document.getElementById('username-status-icon');
    const registerBtn   = document.getElementById('save-btn');

    usernameInput.addEventListener('input', debounce(async () => {
        const username = usernameInput.value.trim();

        statusIcon.hidden = true;
        statusIcon.classList.remove('visible');
        registerBtn.disabled = true;

        if (username.length < 3) {
        statusIcon.src = 'img/cross.svg';
        statusIcon.hidden = false;
        statusIcon.classList.add('visible');
        return;
        }

        statusIcon.src = 'img/loading.svg';
        statusIcon.hidden = false;
        statusIcon.classList.add('visible');
        const csrf = document.querySelector('meta[name="_token"]').content;

        try {
        const response = await fetch("/check-username", {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'credentials': 'include',
            'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ username })
        });

        if (!response.ok) throw new Error('Network response not OK');

        const data = await response.json();

        if (data.success === true) {
            statusIcon.src = 'img/check.svg';
            registerBtn.disabled = false;
        } else {
            statusIcon.src = 'img/cross.svg';
        }
        } catch (err) {
        statusIcon.src = 'img/cross.svg';
        console.error('Error checking username:', err);
        }
    }, 500));
</script>

@endsection
