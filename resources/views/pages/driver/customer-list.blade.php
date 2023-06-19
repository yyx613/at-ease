@extends('components.layouts.layout-front-end')

@section('layout-title', 'test')

@section('content')
@vite(['resources/scss/_customer-list.scss'])

<div id="driver-container" class="{{ $step == 1 ? '' : 'start-trip' }}">
    <!-- Truck illustration -->
    <div id="progress-container">
        <div id="truck-illu-container">
            <img src="/images/truck.png" alt="">
        </div>
        <div id="progress">
            <span class="base-span">{{ $deliveryProgress ?? 0 }}%</span>
            <span id="progress-outer">
                <span id="progress-inner" style="width: {{ $deliveryProgress ?? 0 }}%;"></span>
            </span>
        </div>
    </div>
    
    <!-- Delivery order -->
    <div id="delivery-order-container">
        <h6 class="base-heading">Delivery Order</h6>
        <div id="customer-list-container">
            @foreach($customers as $customer)
                <div class="customer-list">
                    <div class="customer-list-left">
                        <span class="base-span customer-id">#{{ $customer->id }}</span>
                        <span class="base-span customer-name">{{ $customer->name }}</span>
                        <span class="base-span customer-address">Bangi</span>
                    </div>
                    <div class="customer-list-right">
                        @if ($step == 1)
                            <button class="base-button" type="button">
                                @include('components.icons.icon-two-hori-line')
                            </button>
                        @else
                            <a href="{{ route('info', ['id' => $customer->id]) }}" class="base-a">
                                @if($customer->orders->where('status_id', $orderPendingId)->count() <= 0)
                                    <span class="base-span complete-status">Complete</span>
                                @elseif ($customer->orders->where('status_id', $orderPendingId)->count() > 0)
                                    <span class="base-span pending-status">Pending</span>
                                @endif
                                @include('components.icons.icon-right-arrow')
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@section('driver-action-button-text', $step == 1 ? 'Start Trip' : 'End Trip')
@endsection