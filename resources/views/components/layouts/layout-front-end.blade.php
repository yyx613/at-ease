<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
</head>
<body>
    @vite(['resources/scss/_layout-front-end.scss'])

    <div id="layout-front-end">
        <header>
            <h6 class="base-heading">@yield('layout-title')</h6>
        </header>
        <main>
            @yield('content')
        </main>
        <footer>
            @if(Route::currentRouteName() != 'login')
                @if(Route::currentRouteName() == 'cart')
                    <ul class="base-ul order-page-ul">
                        <li class="base-li">
                            <a href="{{ route('select-product') }}" class="base-a">Cancel</a>
                        </li>
                        <li class="base-li">
                            <button class="base-button" type="button" id="confirm-order-btn">Confirm</button>    
                        </li>
                    </ul> 
                @else
                    <ul class="base-ul">
                        <li class="base-li">
                            <a href="{{ route('customer-list') }}" class="base-a">
                                @include('components.icons.icon-truck')
                            </a>
                        </li>
                        <li class="base-li">
                            <button type="button" class="base-button">
                                @include('components.icons.icon-boxes')
                            </button>
                        </li>
                        <li class="base-li">
                            <a href="{{ route('logout') }}" class="base-a">
                                @include('components.icons.icon-logout')
                            </a>
                        </li>
                        <li class="base-li" id="driver-action-button-container">
                            @if($nextStep == route('cart'))
                                <button class="base-button" type="button">
                                    @yield('driver-action-button-text')
                                </button>
                            @else
                                <a href="{{ $nextStep }}" class="base-a">
                                    @yield('driver-action-button-text')
                                </a>
                            @endif
                        </li>
                    </ul>
                @endif
            @endif
        </footer>
    </div>
</body>
</html>