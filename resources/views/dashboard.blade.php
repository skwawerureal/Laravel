@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Overview of your email marketing performance</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-mail-send-line text-2xl text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Campaigns</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ App\Models\Campaign::count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-user-3-line text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Subscribers</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ App\Models\Subscriber::active()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-mail-open-line text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Emails Sent</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ App\Models\SentEmail::where('status', 'sent')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-bar-chart-line text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Open Rate</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @php
                                    $totalSent = App\Models\SentEmail::where('status', 'sent')->count();
                                    $totalOpens = App\Models\EmailAnalytic::where('event_type', 'open')->count();
                                    $avgOpenRate = $totalSent > 0 ? ($totalOpens / $totalSent) * 100 : 0;
                                @endphp
                                {{ number_format($avgOpenRate, 1) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Campaigns</h2>
        <div class="shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $recentCampaigns = App\Models\Campaign::with(['emailLists', 'template'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentCampaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $campaign->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $campaign->sent_count }} / {{ $campaign->total_recipients }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($campaign->open_rate, 1) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $campaign->created_at->format('M j, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No campaigns yet. <a href="{{ route('campaigns.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first campaign</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('campaigns.create') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="ri-add-circle-line text-2xl text-indigo-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Create Campaign</h3>
                        <p class="text-sm text-gray-500">Start a new email campaign</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('email-lists.create') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="ri-contacts-line text-2xl text-green-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Create Email List</h3>
                        <p class="text-sm text-gray-500">Organize your subscribers</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('email-templates.create') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="ri-file-text-line text-2xl text-blue-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Create Template</h3>
                        <p class="text-sm text-gray-500">Design email templates</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('subscribers.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="ri-user-add-line text-2xl text-purple-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Add Subscribers</h3>
                        <p class="text-sm text-gray-500">Import or add manually</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
