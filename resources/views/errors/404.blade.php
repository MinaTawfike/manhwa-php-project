@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto">
        <h1 class="text-6xl font-bold text-red-500 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-300 mb-4">Page Not Found</h2>
        <p class="text-gray-400 mb-8">
            Sorry, the page you are looking for doesn't exist or has been moved.
        </p>
        <div class="space-y-4">
            <a href="{{ route('comics.index') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                Go to Homepage
            </a>
            <br>
            <a href="javascript:history.back()" class="inline-block text-gray-400 hover:text-gray-300 underline">
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
