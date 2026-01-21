@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<div class="px-4 sm:px-0">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Campaign Details</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Create a new email campaign. Fill in the basic information and select your target email lists.
                </p>
            </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form action="{{ route('campaigns.store') }}" method="POST">
                @csrf
                <div class="shadow sm:overflow-hidden sm:rounded-md">
                    <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name</label>
                                <input type="text" name="name" id="name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                                <input type="text" name="subject" id="subject" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('subject') }}">
                                @error('subject')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                                <input type="text" name="from_name" id="from_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('from_name') }}">
                                @error('from_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="from_email" class="block text-sm font-medium text-gray-700">From Email</label>
                                <input type="email" name="from_email" id="from_email" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('from_email') }}">
                                @error('from_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="reply_to" class="block text-sm font-medium text-gray-700">Reply To (Optional)</label>
                                <input type="email" name="reply_to" id="reply_to"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('reply_to') }}">
                                @error('reply_to')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="content" class="block text-sm font-medium text-gray-700">Email Content</label>
                                <textarea name="content" id="content" rows="10" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('content') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Available variables: {{ '{first_name}' }}, {{ '{last_name}' }}, {{ '{full_name}' }}, {{ '{email}' }}, {{ '{unsubscribe_url}' }}
                                </p>
                                @error('content')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Email Lists</label>
                                <div class="mt-2 space-y-2">
                                    @foreach ($emailLists as $list)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="email_lists[]" value="{{ $list->id }}" 
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                @if(old('email_lists') && in_array($list->id, old('email_lists'))) checked @endif>
                                            <span class="ml-2 text-sm text-gray-700">{{ $list->name }} ({{ $list->subscriber_count }} subscribers)</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('email_lists')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="template_id" class="block text-sm font-medium text-gray-700">Template (Optional)</label>
                                <select name="template_id" id="template_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a template</option>
                                    @foreach ($templates as $template)
                                        <option value="{{ $template->id }}" @if(old('template_id') == $template->id) selected @endif>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('template_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule (Optional)</label>
                                <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ old('scheduled_at') }}">
                                @error('scheduled_at')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <a href="{{ route('campaigns.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Create Campaign
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
