@extends('layouts.app')

@section('title', 'Email Campaign')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('campaigns.index') }}" class="text-indigo-600 hover:text-indigo-900">
            <i class="ri-arrow-left-line mr-2"></i>Back to Campaigns
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $campaign->name }}</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $campaign->subject }}
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($campaign->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($campaign->status === 'scheduled') bg-blue-100 text-blue-800
                            @elseif($campaign->status === 'sending') bg-yellow-100 text-yellow-800
                            @elseif($campaign->status === 'sent') bg-green-100 text-green-800
                            @elseif($campaign->status === 'paused') bg-orange-100 text-orange-800
                            @elseif($campaign->status === 'cancelled') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">From</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $campaign->from_name }} &lt;{{ $campaign->from_email }}&gt;
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email Lists</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        @foreach($campaign->emailLists as $list)
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 mr-2 mb-2">
                                {{ $list->name }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Performance</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $campaign->sent_count }}</div>
                                <div class="text-xs text-gray-500">Sent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ number_format($campaign->open_rate, 1) }}%</div>
                                <div class="text-xs text-gray-500">Open Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($campaign->click_rate, 1) }}%</div>
                                <div class="text-xs text-gray-500">Click Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ number_format($campaign->bounce_rate, 1) }}%</div>
                                <div class="text-xs text-gray-500">Bounce Rate</div>
                            </div>
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email Content</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                        <div class="prose max-w-none">
                            {!! $campaign->content !!}
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($campaign->sentEmails->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Sent Emails</h3>
            <div class="shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($campaign->sentEmails->take(10) as $sentEmail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sentEmail->to_email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($sentEmail->status === 'sent') bg-green-100 text-green-800
                                        @elseif($sentEmail->status === 'delivered') bg-blue-100 text-blue-800
                                        @elseif($sentEmail->status === 'bounced') bg-red-100 text-red-800
                                        @elseif($sentEmail->status === 'failed') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($sentEmail->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sentEmail->sent_at ? $sentEmail->sent_at->format('M j, Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sentEmail->opens->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sentEmail->clicks->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
