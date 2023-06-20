@extends('components.layouts.layout-front-end')

@section('layout-title', 'Order Receipt')

@section('content')
@vite(['resources/scss/_receipt.scss', 'resources/js/receipt.js'])

<div id="receipt-container">
    <iframe src="{{ asset('/storage/pdf/' . $invoice_name) }}#toolbar=0" id="pdf-frame"></iframe>
    <div id="print-container">
        <button class="base-button" type="button">
            Print Invoice
        </button>
    </div>
</div>

@section('driver-action-button-text', 'Continue Shopping')
@endsection