@extends('layout')

@section('title', 'Reset Password')

@section('head')
  <link rel="stylesheet" href="css/login.css">
@endsection

@section('content')
<div class="login-wrapper d-flex justify-content-center align-items-center vh-100">
  <div class="card login-card shadow-sm position-relative">
    <div class="card-body">

      <a href="{{ route('login') }}" class="back-link">
        <img src="{{ asset('img/arrow-back.svg') }}"
             alt="Back"
             class="back-icon">
      </a>

      <div class="text-center mb-5">
        <h1 class="poppins-bold">Our Bill</h1>
        <hr class="title-underline">
      </div>

      <form method="POST" action="{{ route('forgot-password.post') }}">
        @csrf

        <h2 class="text-center poppins-semibold light-green mb-5">RESET PASSWORD</h2>
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        <div class="mb-3 input-group">
          <input
            type="text"
            name="username"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Username / ID"
            id="username"
            required
          >
        </div>

        <div class="mb-3 input-group">
          <input
            type="email"
            name="email"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Email"
            id="email"
            required
          >
        </div>

        <div class="mb-3 input-group position-relative">
          <input
            type="password"
            name="password"
            id="password"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="New Password"
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

        <div class="mb-3 input-group otp-group">
            <input
              type="number"
              name="otp"
              id="otp"
              class="form-control border-bottom poppins-regular"
              placeholder="OTP"
              required
            >
            <button
              type="button"
              id="btn-send-otp"
              class="btn-send-otp poppins-medium"
            >Send OTP</button>
          </div>
          <div id="otp-feedback" class="otp-feedback"></div>
          

        <button type="submit"
                class="btn btn-login w-100 mb-4 poppins-medium">
          CHANGE
        </button>
      </form>
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

  document.getElementById('btn-send-otp').addEventListener('click', async function() {
    const btn = this;
    const username = document.getElementById('username').value.trim();
    const email    = document.getElementById('email').value.trim();
    const feedback = document.getElementById('otp-feedback');

    feedback.textContent = '';
    if (!username || !email) {
      feedback.textContent = 'Username dan email harus diisi dulu.';
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Sending...';
    feedback.classList.remove('success');

    try {
      const response = await fetch('/api/send-otp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ username, email })
      });
      const data = await response.json();

      if (response.ok && data.success) {
        feedback.classList.add('success');
        feedback.textContent = 'OTP terkirim ke email Anda.';
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        usernameInput.disabled = true;
        emailInput.disabled = true;
        let sec = 60;
        const timer = setInterval(() => {
          if (sec === 0) {
            clearInterval(timer);
            btn.disabled = false;
            btn.textContent = 'Send OTP';
          } else {
            btn.textContent = `Retry in ${sec--}s`;
          }
        }, 1000);
      } else {
        throw new Error(data.message || 'Gagal kirim OTP');
      }
    } catch (err) {
      feedback.textContent = 'Gagal kirim OTP';
      btn.disabled = false;
      btn.textContent = 'Send OTP';
    }
  });
</script>

@endsection
