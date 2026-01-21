@extends('layouts.app')

@section('title', 'Unsubscribe Successful')

@section('content')
<div class="px-4 sm:px-0">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 text-center">
        <div class="mb-4">
            <i class="ri-checkbox-circle-fill text-5xl text-green-500"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Successfully Unsubscribed</h2>
        <p class="text-gray-600 mb-6">
            You have been successfully removed from our mailing list. We're sorry to see you go!
        </p>
        <p class="text-sm text-gray-500">
            Email: {{ $subscriber->email }}
        </p>
        <div class="mt-6">
            <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
