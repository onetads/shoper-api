<html lang="pl">
<head>
    <meta name="referrer" content="origin">
    <script src="https://dcsaascdn.net/js/dc-sdk-1.0.5.min.js"></script>
    <script src="{{asset('js/shoper.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    @stack('css')
</head>

<body>
@yield('content')
<p class="copyright">
    &copy; Ringer Axel Springer Polska. Wszelkie prawa zastrzeżone.
</p>
@stack('javascript')
</body>
</html>
