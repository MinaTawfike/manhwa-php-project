@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Oops! Something went wrong.</h1>
    <p>Please try again later. If the problem persists, contact support.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>
@endsection
