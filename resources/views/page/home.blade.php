@extends('layout_nonvue')


@section('header')
@include('header.home')
@endsection


@section('content')
<div id="visual-wrap">
    <img id="visual-image" src="{{asset('image/CloudBell-logo.png')}}" alt="">
</div>
@endsection
