<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>@php echo env('APP_NAME'); @endphp</title>
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width,initial-scale=1">
    </head>
    <body>
        <header><div>@yield('header')</div></header>
        <main><div>@yield('content')</div></main>
        <footer><div>@yield('footer')</div></footer>
        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
