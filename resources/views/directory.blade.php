@extends('layouts.app')
@section('title', $kind === 'customers' ? 'Customer Accounts' : 'User Accounts')
@section('content')
<div class="page-heading"><p class="eyebrow">{{ $kind === 'customers' ? 'CUSTOMER DIRECTORY' : 'STAFF DIRECTORY' }}</p><h1>{{ $kind === 'customers' ? 'Customer Accounts' : 'User Accounts' }}</h1><p>{{ $kind === 'customers' ? 'Names and contact details for a quick connection.' : 'The usernames and people behind the counter.' }}</p></div>
@if($errors->has('sort') || $errors->has('direction'))<p role="alert" class="validation">Invalid sorting option. Choose a column heading to sort.</p>@endif
<section class="records" aria-label="{{ $kind === 'customers' ? 'Customer' : 'User' }} directory">
<div class="directory-toolbar">
    <form method="get" action="{{ route($kind.'.index') }}" class="search-form">
        <label for="directory-search">{{ $kind === 'customers' ? 'Search name, email or phone' : 'Search username or full name' }}</label>
        <div class="search-row"><input type="search" id="directory-search" name="q" value="{{ old('q', $q) }}" maxlength="100" @error('q') aria-invalid="true" aria-describedby="q-error" @enderror><input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="direction" value="{{ $direction }}"><button class="primary" type="submit">Search</button><a class="clear" href="{{ route($kind.'.index') }}">Reset</a></div>
        @error('q')<p class="validation" id="q-error">{{ $message }}</p>@enderror
    </form>
    <fieldset class="density" hidden><legend>Row density</legend><div><button type="button" data-density="comfortable" aria-pressed="true">Comfortable</button><button type="button" data-density="compact" aria-pressed="false">Compact</button></div></fieldset>
</div>
<div class="results-line"><p><strong>{{ $records->count() }}</strong> {{ $records->count() === 1 ? 'record' : 'records' }}{{ $q !== '' ? ' matching “'.$q.'”' : '' }}</p><span>FICTIONAL DEMONSTRATION RECORDS</span></div>
<p class="scroll-hint">Scroll the table horizontally to see every {{ $kind === 'customers' ? 'contact detail' : 'column' }}. <span aria-hidden="true">↔</span></p>
<div class="table-scroll" tabindex="0" role="region" aria-label="Account records, scroll horizontally to see all columns">
<table class="table-{{ $kind }}"><caption class="sr-only">{{ $kind === 'customers' ? 'Customer' : 'User' }} accounts. Select a column heading to sort.</caption>
    <thead><tr>@foreach($columns as $column => $label)
        <th scope="col" aria-sort="{{ $sort === $column ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}"><a href="{{ route($kind.'.index', ['q' => $q, 'sort' => $column, 'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc']) }}" aria-label="{{ $label }}: sort {{ $sort === $column && $direction === 'asc' ? 'descending' : 'ascending' }}">{{ $label }} <span aria-hidden="true">{{ $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th>
    @endforeach</tr></thead>
    <tbody>
    @foreach($records as $record)
        <tr>@foreach($columns as $column => $label)
        <td class="{{ $column === 'full_name' ? 'record-name' : 'mono' }} {{ $column === 'id' ? 'record-id' : '' }}">
            @if($column === 'phone' && $record->phone === null)
                <span aria-hidden="true">—</span><span class="sr-only">Phone not provided</span>
            @else
                <div class="cell-value"><span>{{ $record->$column }}</span>
                @if(in_array($column, ['email', 'phone', 'username']))
                    <button type="button" class="copy" data-copy="{{ $record->$column }}" aria-label="Copy {{ strtolower($label) }} for {{ $record->full_name }}" hidden>Copy</button>
                @endif</div>
            @endif
        </td>@endforeach</tr>
    @endforeach
    </tbody>
</table>
@if($records->isEmpty())<div class="empty"><h2>{{ $q !== '' ? 'No matching records' : 'No records available' }}</h2><p>{{ $q !== '' ? 'Try a different name or detail, or reset the search.' : 'This directory has no accounts yet. Please check again later.' }}</p>@if($q !== '')<a href="{{ route($kind.'.index') }}">Reset search</a>@endif</div>@endif
</div>
<p class="table-note">{{ $kind === 'customers' ? 'An em dash means a phone number was not provided.' : 'Usernames identify staff accounts in this demonstration.' }} <span>Read-only directory.</span></p>
</section>
@endsection
