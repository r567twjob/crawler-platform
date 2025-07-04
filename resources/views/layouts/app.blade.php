<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}- @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="container">
        @yield('content')
    </div>
    @yield('scripts')
    @vite('resources/js/app.js')
</body>

</html>
