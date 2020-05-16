@extends('layout')


@section('header')
@include('header.user')
@endsection


@section('content')
<div id="app">
    <nav class="menu">
        <ul>
            <li><router-link to="/">ホーム</router-link></li>
            <li><router-link to="/plan">プラン</router-link></li>
            <li><router-link to="/calendar">カレンダー</router-link></li>
            <li><router-link to="/user">ユーザー</router-link></li>
        </ul>
    </nav>

    <div class="content">
        <router-view></router-view>
    </div>
</div>
@endsection
