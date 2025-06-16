@extends('layout')

@section('title', 'Register')

@section('head')
  <link rel="stylesheet" href="css/login.css">
@endsection

@section('content')
<div class="login-wrapper d-flex justify-content-center align-items-center vh-100">
  <div class="card login-card shadow-sm position-relative">
    <div class="card-body">

      <a href="{{ route('login') }}" class="back-link">
        <img src="img/arrow-back.svg"
             alt="Back"
             class="back-icon">
      </a>

      <div class="text-center mb-3">
        <h1 class="poppins-bold">Our Bill</h1>
        <hr class="title-underline">
      </div>

      <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
        @csrf

        <h2 class="text-center poppins-semibold light-green mb-3">REGISTER</h2>

        <div class="mb-3 input-group">
          <input
            type="text"
            name="name"
            id="name"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Name"
            required
            value="{{ old('name') }}"
          >
        </div>

        <div class="mb-3 input-group position-relative">
          <input
            type="text"
            name="username"
            id="username"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Username / ID"
            required
            value="{{ old('username') }}"
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
        

        <div class="mb-3 input-group">
          <input
            type="email"
            name="email"
            id="email"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Email"
            required
            value="{{ old('email') }}"
          >
        </div>

        <div class="mb-3 input-group position-relative">
          <input
            type="password"
            name="password"
            id="password"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Password"
            required
          >
          <img src="img/eye.svg"
               alt="Toggle visibility"
               class="toggle-eye"
               onclick="toggleVisibility('password', this)">
        </div>

        <div class="mb-4 input-group position-relative">
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Confirm Password"
            required
          >
          <img src="img/eye.svg"
               alt="Toggle visibility"
               class="toggle-eye"
               onclick="toggleVisibility('password_confirmation', this)">
        </div>

        @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <button type="submit"
                id="btn-register"
                class="btn btn-login w-100 mb-4 poppins-medium">
          SIGN UP
        </button>

        <div id="reg-feedback" class="text-center mb-2" style="min-height:1em;"></div>
      </form>

      <div class="social-login text-center mb-3">
        <a href="#" class="btn btn-social mx-1">
          <img src="img/social-media/Google.svg" alt="Google" class="social-icon">
        </a>
        <a href="#" class="btn btn-social mx-1">
          <img src="img/social-media/Facebook.svg" alt="Facebook" class="social-icon">
        </a>
        <a href="#" class="btn btn-social mx-1">
          <img src="img/social-media/Apple.svg" alt="Apple" class="social-icon">
        </a>
      </div>

      <p class="text-center small mb-0 poppins-medium">
        Already have an ACCOUNT?
        <a href="{{ route('login') }}"
           class="fw-semibold text-decoration-none green-link">
          Login
        </a>
      </p>
    </div>
  </div>
</div>

<script>
  function toggleVisibility(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.src = '{{ asset("img/eye-off.svg") }}';
    } else {
      input.type = 'password';
      icon.src = '{{ asset("img/eye.svg") }}';
    }
  }

  function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  }

  const usernameInput = document.getElementById('username');
  const statusIcon    = document.getElementById('username-status-icon');
  const registerBtn   = document.getElementById('btn-register');

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

    try {
      const response = await fetch("/api/check-username", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
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
