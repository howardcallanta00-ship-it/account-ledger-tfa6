<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'POS Account Ledger') · Account Ledger</title>
    <link rel="stylesheet" href="{{ asset('assets/ledger.css') }}">
    <script src="{{ asset('assets/ledger.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<div class="shell">
<aside class="rail">
    <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">AL</span><span>Account <br>Ledger</span></a>
    <p class="rail-label">RETAIL POS / DIRECTORY</p>
    <nav aria-label="Main navigation">
    @foreach (['home' => ['01', 'Home'], 'customers.index' => ['02', 'Customer Accounts'], 'users.index' => ['03', 'User Accounts'], 'about' => ['04', 'About']] as $route => [$number, $label])
        <a href="{{ route($route) }}" @if(request()->routeIs($route)) aria-current="page" @endif><span class="nav-number" aria-hidden="true">{{ $number }}</span>{{ $label }}</a>
    @endforeach
    </nav>
    <div class="rail-note"><span class="small-rule"></span>Fictional records.<br>For demonstration only.</div>
</aside>
<div class="workspace">
    <header class="topline"><span>SMALL STORE / ACCOUNT DIRECTORY</span><span>READ-ONLY PROTOTYPE</span></header>
    <main id="main" tabindex="-1">@yield('content')</main>
    <footer><span>POS Account Ledger</span><span>Fictional demonstration content</span></footer>
</div>
</div>
<div class="sr-only" role="status" aria-live="polite" id="copy-status"></div>
<div class="copy-toast" id="copy-toast" hidden></div>
</body>
</html>
