@extends('layout')

@section('title', 'Login')

@section('head')
    <link rel="stylesheet" href="css/login.css">
@endsection

@section('content')
<div class="login-wrapper d-flex justify-content-center align-items-center vh-100">
  <div class="card login-card shadow-sm">
    <div class="card-body">
      <div class="text-center mb-4">
        <h1 class="poppins-bold">Our Bill</h1>
        <img src="img/OurBill.png" alt="Our Bill Logo" class="logo-img my-2" style="width: 80px; height: auto;">
        <p class="subtitle poppins-regular">Easy Pay for Split Bill</p>
      </div>
      <hr>

      <form method="POST" action="{{ route('login.post') }}" enctype="multipart/form-data">
        @csrf
        <h2 class="text-center mb-3 poppins-semibold light-green">LOGIN</h2>

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        <div class="mb-3 input-group">
          <span class="input-group-text bg-transparent border-0">
            <img src="img/at.svg"
                 alt="@ icon"
                 class="form-icon">
          </span>
          <input
            type="text"
            name="login"
            class="form-control border-0 border-bottom poppins-regular"
            placeholder="Username / Email / ID"
            required
            value="{{ old('login') }}"
          >
        </div>
          <div class="mb-3 input-group position-relative">
            <span class="input-group-text bg-transparent border-0">
              <img src="img/lock.svg"
                   alt="lock icon"
                   class="form-icon">
            </span>
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

        <div class="text-center mb-3">
          <a href="{{ route('forgot-password') }}" class="text-decoration-none light-green poppins-semibold">FORGOT PASSWORD?</a>
        </div>

        <button type="submit" class="btn btn-login w-100 mb-4 poppins-medium">
          LOGIN
        </button>
      </form>

      <div class="social-login text-center mb-3">
        <a href="{{ route('login.google') }}" class="btn btn-social mx-1">
          <img src="img/social-media/Google.svg" alt="Google">
        </a>
        <a href="" class="btn btn-social mx-1">
          <img src="img/social-media/Facebook.svg" alt="Google">
        </a>
        <a href="" class="btn btn-social mx-1">
          <img src="img/social-media/Apple.svg" alt="Google">
        </a>
      </div>

      <p class="text-center small mb-0 poppins-medium">
        Don’t have an ACCOUNT?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none green-link">Sign Up</a>
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
</script>
@endsection
