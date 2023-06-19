<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
</head>
<body>
    @vite(['resources/scss/_layout-admin.scss'])

    <div id="layout-admin">
        <header>
            <h6 class="base-heading">@yield('layout-title')</h6>
        </header>
        <main>
            @if(!$isHomePage)
                <div id="redirect-to-home-container">
                    <a href="{{ route('admin-home') }}" class="base-a">
                        @include('components.icons.icon-left-arrow')
                        <span class="base-span">Back to home</span>
                    </a>
                </div>
            @endif
            @yield('content')
        </main>
        <!-- <footer>
        </footer> -->
    </div>
</body>
</html>