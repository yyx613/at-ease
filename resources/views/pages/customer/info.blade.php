@extends('components.layouts.layout-front-end')

@section('layout-title', 'Customer Info Page')

@section('content')
@vite(['resources/scss/_customer-info.scss'])

<div id="avatar-container">
    <div id="avatar-container-top">
        <div id="img-container">
            <img src="https://images.unsplash.com/photo-1672932704930-a35b822081ee?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1887&q=80" alt="">
        </div>
    </div>
    <div id="avatar-container-bottom">
        <h3 class="base-heading">{{ $user->name }}</h3>
    </div>
</div>

<div id="info-container">
    <div class="info">
        <div class="info-label">
            @include('components.icons.icon-id')
            <span class="base-span">Identity</span>
        </div>
        <div class="info-content">
            <span class="base-span">#{{ $user->id }}</span>
        </div>
    </div>
    <div class="info">
        <div class="info-label">
            @include('components.icons.icon-credit-card')
            <span class="base-span">Credit</span>
        </div>
        <div class="info-content">
            <span class="base-span">RM{{ $user->credit }}</span>
        </div>
    </div>
    <div class="info">
        <div class="info-label">
            @include('components.icons.icon-truck')
            <span class="base-span">Delivery Status</span>
        </div>
        <div class="info-content">
            @if($user->orders->where('status_id', $orderPendingId)->count() <= 0)
                <span class="base-span order-status complete-status">Complete</span>
            @elseif ($user->orders->where('status_id', $orderPendingId)->count() > 0)
                <span class="base-span order-status pending-status">Pending</span>
            @endif
        </div>
    </div>
</div>

@section('driver-action-button-text', 'New Order')
@endsection