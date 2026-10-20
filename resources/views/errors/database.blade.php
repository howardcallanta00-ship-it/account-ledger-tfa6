@extends('layouts.app')
@section('title', 'Directory unavailable')
@section('content')
<div class="page-heading"><p class="eyebrow">CONNECTION INTERRUPTED</p><h1>Directory temporarily unavailable</h1><p>We couldn’t retrieve the records. Please try again in a moment.</p></div>
<a class="retry primary" href="{{ request()->fullUrl() }}">Try again</a>
@endsection
