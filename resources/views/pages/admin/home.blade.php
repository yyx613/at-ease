@extends('components.layouts.layout-admin')

@section('layout-title', 'Admin')

@section('content')
@vite(['resources/scss/_home.scss', 'resources/js/home.js'])

<div id="home-container">
    @include('components.model.model-delete-user')
    <div id="action-container">
        <a href="{{ route('create-user') }}" class="base-a">Create User</a>
    </div>
    @if (session('success') || session('error'))
        <div id="submit-status-container">
            @if (session('success')) <p class="base-p success-msg">{{ session('success') }}</p> @endif
            @if (session('error')) <p class="base-p error-msg">{{ session('error') }}</p> @endif
        </div>
    @endif
    <div id="user-list-container">
        @forelse($users as $user)
            <div class="user-container">
                <div class="user-id">
                    <span class="base-span">#{{ $user->id }}</span>
                </div>
                <div class="user-name">
                    <span class="base-span">{{ $user->name }}</span>
                </div>
                <div class="user-email">
                    <span class="base-span">{{ $user->email }}</span>
                </div>
                <div class="user-role">
                    <span class="base-span">{{ $user->role->name }}</span>    
                </div>
                <div class="user-action">
                    <a href="{{ route('edit-user', ['id' => $user->id]) }}" class="base-a">
                        @include('components.icons.icon-edit')
                    </a>
                    <button class="base-button delete-user-trigger" data-id="{{ $user->id }}" data-username="{{ $user->name }}">
                        @include('components.icons.icon-delete')
                    </button>
                </div>
            </div>
        @empty
            <p class="base-p">There's no user created</p>
        @endforelse
    </div>
</div>
@endsection