@extends('components.layouts.layout-front-end')

@section('layout-title', 'New Order')

@section('content')
@vite(['resources/scss/_cart.scss', 'resources/js/cart.js'])

<div id="order-summary-container">
    <div id="order-summary-top">
        <h6 class="base-heading">Order Summary</h6>
        <a href="{{ route('select-product') }}" class="base-a">Add Items</a>
    </div>
    <div id="order-summary-bottom">
        <div id="product-list">
            @foreach($cart as $prod)
                <div class="product-container">
                    <div class="product-left">
                        <div class="img-container">
                            <img src="/images/{{ $prod->img_name }}" alt="">
                        </div>
                    </div>
                    <div class="product-mid">
                        <span class="base-span">{{ $prod->name }}</span>
                    </div>
                    <div class="product-right">
                        <span class="base-span">Qty {{ $prod->qty }} x RM{{ $prod->price }}</span>
                        <span class="base-span">RM{{ $prod->total_price }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="total-price-container">
            <span class="base-span">Total Price</span>
            <span class="base-span">RM{{ $cartTotalPrice }}</span>
        </div>
    </div>
</div>

<div id="payment-detail-container">
    <div id="payment-detail-top">
        <h6 class="base-heading">Payment Details</h6>
    </div>
    <div id="payment-detail-bottom">
        <!-- <div id="paid-amount-container">
            <span class="base-span">Paid Amount</span>
            <span class="base-span">RM2.00</span>
        </div> -->
        <div id="past-due-amount-container">
            <span class="base-span">Past Due Amount</span>
            <span class="base-span">RM{{ $user->credit }}</span>
        </div>
        <div id="credit-note-container">
            <span class="base-span">Credit Note (Update)</span>
            <span class="base-span">RM{{ $cartTotalPrice }}</span>
        </div>
        <div id="remark-container">
            <span class="base-span">Remarks</span>
            <form action="{{ route('cart-submit') }}" method="post" id="cart-form">
                @csrf
                <textarea name="remark" id="remark" rows="8" placeholder="Write your remark here..."></textarea>
            </form>
        </div>
    </div>
</div>
@endsection