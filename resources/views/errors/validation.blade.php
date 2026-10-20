@extends('layouts.app')
@section('title', 'Check directory options')
@section('content')
<div class="page-heading"><p class="eyebrow">CHECK YOUR SEARCH</p><h1>Check directory options</h1><div role="alert"><ul>@foreach($messages as $message)<li>{{ $message }}</li>@endforeach</ul></div><p>Use a search of up to 100 characters and select sorting from a column heading.</p></div>
<a class="primary retry" href="{{ url()->current() }}">Reset directory options</a>
@endsection
