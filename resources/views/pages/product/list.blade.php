@extends('components.layouts.layout-front-end')

@section('layout-title', 'Select Product')

@section('content')
@vite(['resources/scss/_product-list.scss', 'resources/js/product-list.js'])

@include('components.model.model-select-product')

@if (session('info') || session('error'))
    <div id="submit-status-container">
        @if (session('info')) <p class="base-p info-msg">{{ session('info') }}</p> @endif
        @if (session('error')) <p class="base-p error-msg">{{ session('error') }}</p> @endif
    </div>
@endif

<div id="product-list-container">
    @foreach($products as $product)
        <div class="product-container" data-id="{{ $product->id }}" data-qty="{{ $product->qty }}">
            <div class="product-container-top">
                <div class="img-container">
                    <img src="/images/{{ $product->img_name }}" alt="">
                </div>
            </div>
            <div class="product-container-bottom">
                <h2 class="base-heading product-name" data-id="{{ $product->id }}">{{ $product->name }}</h2>
                <span class="base-span product-price" data-id="{{ $product->id }}" data-price="{{ $product->price }}">RM{{ $product->price }}</span>
            </div>
        </div>
    @endforeach
</div>

<form action="{{ route('select-product-submit') }}" method="post" id="form-submit">
    @csrf
    <input type="text" name="cart" id="cart-input" hidden>
</form>

@section('driver-action-button-text', 'Checkout')
@endsection