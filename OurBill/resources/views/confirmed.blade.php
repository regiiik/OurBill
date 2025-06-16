@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
@endsection

@section('content')
<style>
    body {
        margin: 0;
        padding: 0;
    }
    
    .confirmed-container {
        background-color: #f5ffed;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    
    .check-circle {
        width: 100px;
        height: 100px;
        background-color: #7d9d61;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .confirmation-text {
        font-size: 24px;
        font-weight: 500;
        color: #4a4a4a;
        text-align: center;
    }

    
    /* Ini animasi lingkarannya mau lu ganti gamasalah*/
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-15px);
        }
        60% {
            transform: translateY(-7px);
        }
    }
    
    .animated-circle {
        animation: bounce 2s ease infinite;
    }
</style>
<div class="container">
    <div class="confirmed-container">
        <div class="check-circle animated-circle">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <div class="confirmation-text">Payment Confirmed</div>
    </div>
</div>


<script>
    // Buat langsung balik ke home setelah 5 detik
    setTimeout(() => {
        window.location.href = '/home';
    }, 3000);
</script>
@endsection
