@extends('components.layouts.layout-front-end')

@section('layout-title', 'New Order')

@section('content')
@vite(['resources/scss/_cart.scss', 'resources/js/cart.js'])

<div id="order-summary-container">
    <div id="order-summary-top">
        <h6 class="base-heading">Order Summary</h6>
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
                        @if(
                            (isset($user->foc->type) && $user->foc->type == 2 && $user->foc->product_id == $prod->id) ||
                            (isset($user->foc->type) && $user->foc->type == 3 && $user->foc->product_id == $prod->id)
                        )
                            <span class="base-span ori-price">RM{{ $prod->total_price }}</span>
                            <span class="base-span ttl-price-foc">RM{{ $prod->total_price_after_foc }}</span>
                        @else
                            <span class="base-span">RM{{ $prod->total_price }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if (isset($user->foc->type) && $user->foc->type == 1)
            <div id="foc-container">
                <div id="foc-top">
                    <span class="base-span">You're in<span class="base-span foc-type">free of charge</span></span>
                </div>
                <div id="foc-bottom"></div>
            </div>
        @endif
        <div id="total-price-container">
            <div id="ttl-price-left">
                <span class="base-span">Total Price</span>
            </div>
            <div id="ttl-price-right">
                @if (isset($user->foc->type) && $hasFoc)
                    <span class="base-span ori-price">RM{{ $cartTotalPrice }}</span>
                    <span class="base-span foc-price">RM{{ $cartTotalPriceFoc }}</span>
                @else
                    <span class="base-span">RM{{ $cartTotalPrice }}</span>
                @endif
            </div>
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
            <span class="base-span">RM{{ $cartTotalPriceFoc }}</span>
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