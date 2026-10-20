@extends('layouts.app')
@section('title', 'POS Account Ledger')
@section('content')
<div class="page-heading home-heading"><p class="eyebrow">THE STORE REFERENCE BOOK</p><h1>POS Account Ledger</h1><p>People behind the counter.<br>People on the other side.</p></div>
<section class="directory-choices" aria-label="Choose an account directory">
    <a class="directory-choice" href="{{ route('customers.index') }}"><span class="index-number">01</span><div><p class="eyebrow">CUSTOMER DIRECTORY</p><h2>Customer Accounts</h2><p>Find customer names and contact details.</p><span class="open-label">Open customer directory <span aria-hidden="true">↗</span></span></div></a>
    <a class="directory-choice" href="{{ route('users.index') }}"><span class="index-number">02</span><div><p class="eyebrow">STAFF DIRECTORY</p><h2>User Accounts</h2><p>Look up staff usernames and full names.</p><span class="open-label">Open user directory <span aria-hidden="true">↗</span></span></div></a>
</section>
<p class="home-note">A practical reference for the shop floor. Browse the directories, search for a person, and copy the detail you need.</p>
@endsection
