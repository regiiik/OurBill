@extends('layout')

@section('head')
    <link rel="stylesheet" href="css/layout.css">
@endsection

@section('content')
<style>
    .back-arrow {
        text-decoration: none;
        color: black;
        font-weight: bold;
        font-size: 24px;
        padding: 10px;
    }

    .page-header {
        padding: 20px 20px 0;
    }

    .page-title {
        color: #6b8e23;
        font-size: 18px;
        margin: 5px 0;
        font-weight: 600;
    }

    .page-subtitle {
        font-size: 28px;
        font-weight: bold;
        margin: 5px 0 20px;
    }

    .bill-container {
        padding: 0 20px;
    }

    .group-title {
        font-weight: bold;
        margin-bottom: 15px;
    }

    .person-card {
        margin-bottom: 25px;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .person-info {
        display: flex;
        align-items: center;
    }

    .person-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
    }

    .person-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .person-name {
        font-size: 14px;
    }

    .person-total {
        font-weight: bold;
        font-size: 18px;
    }

    .person-checkbox {
        position: relative;
    }
    .person-checkbox input[type="checkbox"] {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .person-checkbox .custom-box {
        width: 24px;
        height: 24px;
        border: 2px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 4px;
        font-size: 18px;
        line-height: 18px;
        user-select: none;
    }
    .person-checkbox input[type="checkbox"]:checked:disabled + .custom-box {
        background-color: #6b8e23;
        border-color: #6b8e23;
        color: white;
        cursor: not-allowed;
    }
    .person-checkbox input[type="checkbox"]:disabled:not(:checked) + .custom-box {
        background-color: gray;
        border-color: gray;
        color: white;
        cursor: not-allowed;
    }
    .person-checkbox input[type="checkbox"]:checked:not(:disabled) + .custom-box {
        background-color: #6b8e23;
        border-color: #6b8e23;
        color: white;
    }

    .item-details {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #777;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-top: 10px;
    }

    .summary-section {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px solid #ddd;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .summary-label {
        font-size: 14px;
        text-align: right;
    }

    .summary-value {
        font-size: 14px;
        min-width: 80px;
        text-align: right;
    }

    .total-row {
        font-weight: bold;
    }

    .confirm-btn {
        background-color: #7d9d61;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        font-weight: bold;
        width: 100%;
        margin: 20px 0;
        cursor: pointer;
        text-transform: uppercase;
    }
    .confirm-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="container">
    <div class="page-header">
        <a href="/bill3" class="back-arrow">←</a>
        <div class="page-title">Our Bill</div>
        <div class="page-subtitle">TOTAL AMOUNT</div>
    </div>

    <div class="bill-container">
        <div class="group-title">McD's Bill</div>

        <form action="{{ route('confirm', [$bill->id] ) }}" method="POST" id="confirmForm">
            @csrf

            @foreach ($users as $user)
                @php
                    $isPaid = $user['user']['is_paid'];
                    $userId = $user['user']['id'];
                @endphp

                <div class="person-card">
                    <div class="person-info">
                        <div class="person-avatar">
                            <img src="{{ 'storage/' . $user['user']['profile'] }}"
                                 alt="{{ $user['user']['name'] }}">
                        </div>
                        <div>
                            <div class="person-name">{{ $user['user']['name'] }}'s total</div>
                            <div class="person-total">
                                Rp. {{ number_format($user['total'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="person-checkbox">
                        <input
                            type="checkbox"
                            name="confirmed[]"
                            value="{{ $userId }}"
                            id="user-{{ $userId }}"
                            {{ $isPaid ? 'checked disabled' : '' }}
                            {{ $bill->created_by != auth()->id() ? 'disabled' : '' }}
                        >
                        <label for="user-{{ $userId }}" class="custom-box">
                        </label>
                    </div>
                </div>

                <div style="margin-left: 55px; margin-top: 10px;">
                    <div class="item-details font-bold">
                        <div>Name item</div>
                        <div>Quantity</div>
                        <div>Price</div>
                    </div>

                    @foreach ($user['items'] as $item)
                        <div class="item-details">
                            <div>{{ $item['name'] }}</div>
                            <div>{{ $item['qty'] }}</div>
                            <div>Rp. {{ number_format($item['total'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="summary-section">
                <div class="summary-row">
                    <div class="summary-label">Subtotal :</div>
                    <div class="summary-value">
                        Rp. {{ number_format($subtotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Tax :</div>
                    <div class="summary-value">
                        Rp. {{ number_format($tax, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-row total-row">
                    <div class="summary-label">Total amount :</div>
                    <div class="summary-value">
                        Rp. {{ number_format($total_amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <button
                type="submit"
                class="confirm-btn"
                id="confirmBtn"
                disabled
            >
                CONFIRM PAYMENT
            </button>
        </form>
    </div>
</div>

<script>
    const unpaidCheckboxes = Array.from(
        document.querySelectorAll('input[name="confirmed[]"]:not([disabled]):not(:checked)')
    );
    const confirmBtn = document.getElementById('confirmBtn');

    function updateButtonState() {
        const anyChecked = unpaidCheckboxes.some(cb => cb.checked);
        confirmBtn.disabled = !anyChecked;
    }

    unpaidCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const customBox = cb.nextElementSibling;
            if (cb.checked) {
                customBox.innerHTML = '✓';
            } else {
                customBox.innerHTML = '';
            }
            updateButtonState();
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateButtonState();
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateButtonState();

        const disabledCheckboxes = document.querySelectorAll('input[name="confirmed[]"][disabled]:checked');
        disabledCheckboxes.forEach(cb => {
            const customBox = cb.nextElementSibling;
            customBox.innerHTML = '✓';
        });
    });
</script>
@endsection
