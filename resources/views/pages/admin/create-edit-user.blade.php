@extends('components.layouts.layout-admin')

@section('layout-title', $formMode === 'create' ? 'Create User' : 'Edit User')

@section('content')
@vite(['resources/scss/_create-edit-user.scss', 'resources/js/create-edit-user.js'])

<div id="create-edit-user-container">
    @if (session('success') || session('error'))
        <div id="submit-status-container">
            @if (session('success')) <p class="base-p success-msg">{{ session('success') }}</p> @endif
            @if (session('error')) <p class="base-p error-msg">{{ session('error') }}</p> @endif
        </div>
    @endif
    <form action="{{ $formMode === 'create' ? route('create-user-submit') : route('edit-user-submit', ['id' => $user->id]) }}" method="post" id="create-edit-user-form" data-mode="{{ $formMode }}">
        @csrf
        <div id="form-left">
            <div class="form-title-container">
                <h6 class="base-heading">Info</h6>
            </div>
            <div id="info-form-container">
                <div class="input-container">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="{{ $user->name ?? old('name') }}">
                    @error('name') <p class="base-p err-msg">{{ $message }}</p> @enderror
                </div>
                <div class="input-container">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ $user->email ?? old('email') }}" @if($formMode !== 'create') disabled @endif>
                    @error('email') <p class="base-p err-msg">{{ $message }}</p> @enderror
                </div>
                @if(!isset($user) || (isset($user) && $user->role_id == $roleDriverId))
                    <div class="input-container" data-name="password">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                        @error('password') <p class="base-p err-msg">{{ $message }}</p> @enderror
                    </div>
                @endif
                <div class="input-container">
                    <label for="role">Role</label>
                    <select name="role" id="role" @if($formMode !== 'create') disabled @endif>
                        <option value="">Select a role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @if(old('role') == $role->id || (isset($user) && $user->role_id == $role->id)) selected @endif>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="base-p err-msg">{{ $message }}</p> @enderror
                </div>
                @if(!isset($user) || (isset($user) && $user->role_id != $roleDriverId))
                    <div class="input-container" data-name="driver">
                        <label for="driver">Driver</label>
                        <select name="driver" id="driver">
                            <option value="">Select a driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" @if(old('driver') == $driver->id || (isset($user) && $user->driver_id == $driver->id)) selected @endif>{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        @error('driver') <p class="base-p err-msg">{{ $message }}</p> @enderror
                    </div>
                @endif
                <div class="input-container" data-name="foc">
                    <div class="selection-container">
                        <div class="selection-top">
                            <input type="radio" id="foc-1" name="foc" value="foc-1" @if((isset($user->foc->type) && $user->foc->type == 1) || old('foc') == 'foc-1') checked @endif>
                            <label for="foc-1">Free of charge</label>
                        </div>
                    </div>
                    <div class="selection-container">
                        <div class="selection-top">
                            <input type="radio" id="foc-2" name="foc" value="foc-2" @if((isset($user->foc->type) && $user->foc->type == 2) || old('foc') == 'foc-2') checked @endif>
                            <label for="foc-2">Free first<strong>N</strong>pack on specific product</label>
                        </div>
                        <div class="selection-bottom">
                            <input type="number" id="foc-2-n" name="foc_2_n" placeholder="N" value="{{ $user->foc->foc_2_val ?? old('foc_2_n') }}">
                            <select name="foc_2_prod" id="foc_2_prod">
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @if((isset($user->foc->product_id) && $user->foc->type == 2 && $user->foc->product_id == $product->id) || old('foc_2_prod') == $product->id) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('foc_2_n') <p class="base-p err-msg">{{ $message }}</p> @enderror
                        @error('foc_2_prod') <p class="base-p err-msg">{{ $message }}</p> @enderror
                    </div>
                    <div class="selection-container">
                        <div class="selection-top">
                            <input type="radio" id="foc-3" name="foc" value="foc-3" @if((isset($user->foc->type) && $user->foc->type == 3) || old('foc') == 'foc-3') checked @endif>
                            <label for="foc-3">Free <strong>N</strong> pack on every<strong>M</strong>pack on specific product</label>
                        </div>
                        <div class="selection-bottom">
                            @php
                                if(isset($user->foc->foc_3_val)) {
                                    $foc_3_n_m = explode('-', $user->foc->foc_3_val);
                                }
                            @endphp
                            <input type="number" id="foc-3-n" name="foc_3_n" placeholder="N" value="{{ $foc_3_n_m[0] ?? old('foc_3_n') }}">
                            <input type="number" id="foc-3-m" name="foc_3_m" placeholder="M" value="{{ $foc_3_n_m[1] ?? old('foc_3_m') }}">
                            <select name="foc_3_prod" id="foc_3_prod">
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @if((isset($user->foc->product_id) && $user->foc->type == 3 && $user->foc->product_id == $product->id) || old('foc_3_prod') == $product->id) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('foc_3_n') <p class="base-p err-msg">{{ $message }}</p> @enderror
                        @error('foc_3_m') <p class="base-p err-msg">{{ $message }}</p> @enderror
                        @error('foc_3_prod') <p class="base-p err-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="submit-container">
                    <button class="base-button" type="submit">{{ $formMode === 'create' ? 'Create User' : 'Edit User' }}</button>
                </div>
            </div>
        </div>
        <div id="form-right" @if(old('role') == $roleDriverId || (isset($user) && $user->role_id == $roleDriverId)) style="display: none;" @endif>
            <div class="form-title-container">
                <h6 class="base-heading">Special Price</h6>
            </div>
            <div id="product-parent-container">
                <div id="product-create-container">
                    <div class="input-container">
                        <label for="product-name">Product Name</label>
                        <select name="product-name" id="product-name">
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-img="{{ $product->img_name }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-container">
                        <label for="product-price">Product Price
                            <span class="base-span" id="default-price"></span>
                        </label>
                        <input type="number" id="product-price" name="product-price" step="0.01">
                        <p class="base-p">Max Price: RM100,000</p>
                    </div>
                    <div class="submit-container">
                        <button class="base-button" type="button" id="assign-product-btn">Assign To User</button>
                    </div>
                </div>
                <div id="product-list-container" data-specialpriceinput="{{ $specialPriceInput ?? NULL }}">
                    <p class="base-p" id="no-special-price-msg">There's no special price for this user.</p>
                    @if ($formMode !== 'create')
                        @foreach($user->products as $product)
                            <div class="product-container" data-id="{{ $product->id }}">
                                <div class="product-left">
                                    <div class="img-container">
                                        <img src="/images/{{ $product->img_name }}" alt="">
                                    </div>
                                </div>
                                <div class="product-mid">
                                    <span class="base-span">{{ $product->name }}</span>
                                    <span class="base-span">RM{{ $product->pivot->special_price ?? $product->price }}</span>
                                </div>
                                <div class="product-right">
                                    <button class="base-button product-delete-btn" type="button" data-id="{{ $product->id }}">
                                        @include('components.icons.icon-delete')
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="product-container-sample">
                    <div class="product-container" data-id="{product-id}">
                        <div class="product-left">
                            <div class="img-container">
                                <img src="{product-img}" alt="">
                            </div>
                        </div>
                        <div class="product-mid">
                            <span class="base-span">{product-name}</span>
                            <span class="base-span">{product-price}</span>
                        </div>
                        <div class="product-right">
                            <button class="base-button product-delete-btn" type="button" data-id="{product-id}">
                                @include('components.icons.icon-delete')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection