@extends('layout_nonvue')

@section('header')
@include('header.home')
@endsection


@section('content')
<p style="color: white;">{{ $message ?? 'No Message' }}</p>
<a href="/">戻る</a>
@endsection
