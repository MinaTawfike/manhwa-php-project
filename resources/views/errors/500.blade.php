@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto">
        <h1 class="text-6xl font-bold text-red-500 mb-4">500</h1>
        <h2 class="text-2xl font-semibold text-gray-300 mb-4">Server Error</h2>
        <p class="text-gray-400 mb-8">
            Oops! Something went wrong on our end. Please try again later.
        </p>
        
        @if(session('error'))
            <div class="bg-red-900/20 border border-red-500 text-red-400 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="space-y-4">
            <a href="{{ route('comics.index') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                Go to Homepage
            </a>
            <br>
            <button onclick="location.reload()" class="inline-block text-gray-400 hover:text-gray-300 underline">
                Try Again
            </button>
        </div>
    </div>
</div>
@endsection
