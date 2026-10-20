@extends('layouts.app')
@section('title', 'About')
@section('content')
<div class="page-heading"><p class="eyebrow">BEHIND THE LEDGER</p><h1>A small, useful reference.</h1><p>Customer contact details and staff account names, in one readable place.</p></div>
<article class="about-copy"><h2>Made for a quick lookup</h2><p>This read-only retail POS prototype helps store staff find a customer’s contact details or identify a staff account. All records are fictional demonstration content. There are no account changes, sales, or payment functions.</p>
<h2>From request to record</h2><p>Each directory follows this server-side data flow:</p>
<ol class="data-flow"><li><strong>Route</strong><span>Receives the page request</span></li><li><strong>Controller</strong><span>Validates search and sort</span></li><li><strong>Eloquent model</strong><span>Queries account records</span></li><li><strong>MySQL</strong><span>Stores persistent data</span></li><li><strong>Blade view</strong><span>Renders the directory</span></li></ol>
<h2>A repeatable foundation</h2><p>Migrations define the customer and user tables. Seeders insert the fictional records and can run again without duplicating them or removing unrelated entries.</p></article>
@endsection
