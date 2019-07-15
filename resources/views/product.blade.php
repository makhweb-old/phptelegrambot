
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:site_name" content="KFC 🍔">
    <meta name="telegram:channel" content="@kfcuzbekistanoficial">
    <meta property="og:image" content="{{$product->photo}}">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-4">
            <div class="container">
                <div class="product">
                    <h1 class="title">{{$product->name}}</h1>
                    <p>{{$product->description}}</p>
                </div>
            </div>
            <hr>
        </main>
    </div>
</body>
</html>
