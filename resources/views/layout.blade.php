<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>@php echo env('APP_NAME'); @endphp</title>
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="bot_id" content="@php echo env('LINE_BOT_ID'); @endphp">
    </head>
    <body>
        <header><div>@yield('header')</div></header>
        <main><div>@yield('content')</div></main>
        <footer><div>@yield('footer')</div></footer>
        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
