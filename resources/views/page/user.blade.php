@extends('layout')


@section('header')
@include('header.user')
@endsection


@section('content')
<div id="app">
    <menu-component></menu-component>
    <loading-view-component></loading-view-component>
    <div class="content">
        <router-view></router-view>
    </div>

</div>
@endsection
