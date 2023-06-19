@extends('components.layouts.layout-front-end')

@section('layout-title', 'Login Page')

@section('content')
@vite(['resources/scss/_login.scss'])

<div id="login-container">
    <form action="{{ route('login-submit') }}" method="post">
        @csrf
        <div class="input-container">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email') <p class="base-p err-msg">{{ $message }}</p> @enderror
        </div>
        <div class="input-container">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            @error('password') <p class="base-p err-msg">{{ $message }}</p> @enderror
        </div>
        <div class="submit-container">
            <button class="base-button" type="submit">Login</button>
        </div>
    </form>
</div>

@section('driver-action-button-text', 'Start Trip')
@endsection