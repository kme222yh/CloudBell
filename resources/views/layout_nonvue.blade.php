<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>@php echo env('APP_NAME'); @endphp</title>
        <link rel="icon" type="image/png" href="{{asset('favicon.png')}}">
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
        <meta name="viewport" content="width=device-width,initial-scale=1">
    </head>
    <body>
        <header><div>@yield('header')</div></header>
        <main><div>@yield('content')</div></main>
        <footer><div>@yield('footer')</div></footer>
    </body>
</html>
