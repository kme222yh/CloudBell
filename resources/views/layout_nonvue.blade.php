<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>@php echo env('APP_NAME'); @endphp</title>
        <link rel="icon" type="image/png" href="{{asset('favicon.png')}}">
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@php echo env('TWITTER_ID'); @endphp" />
        <meta name="twitter:creator" content="@php echo env('TWITTER_ID'); @endphp" />
        <meta property="og:url" content="@php echo env('APP_URL'); @endphp" />
        <meta property="og:title" content="@php echo env('APP_NAME'); @endphp" />
        <meta property="og:description" content="@php echo env('TWITTER_DESCRIPTION'); @endphp" />
        <meta property="og:image" content="{{ asset('twitter.png') }}" />

    </head>
    <body>
        <header><div>@yield('header')</div></header>
        <main><div>@yield('content')</div></main>
        <footer><div>@yield('footer')</div></footer>
    </body>
</html>
