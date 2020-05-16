@extends('layout')

@section('title', 'error')


@section('content')
<p style="color: white;">{{ $message ?? 'No Message' }}</p>
<a href="/">戻る</a>
@endsection
