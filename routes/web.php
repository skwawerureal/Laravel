<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\EmailListController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\EmailTemplateController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('campaigns', CampaignController::class);
Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
Route::post('campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
Route::post('campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');

Route::resource('email-lists', EmailListController::class);
Route::post('email-lists/{emailList}/import', [EmailListController::class, 'import'])->name('email-lists.import');
Route::post('email-lists/{emailList}/add-subscriber', [EmailListController::class, 'addSubscriber'])->name('email-lists.add-subscriber');
Route::delete('email-lists/{emailList}/subscribers/{subscriber}', [EmailListController::class, 'removeSubscriber'])->name('email-lists.remove-subscriber');

Route::resource('subscribers', SubscriberController::class);
Route::resource('email-templates', EmailTemplateController::class);
Route::get('email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');

Route::get('/unsubscribe/{token}', function ($token) {
    $subscriber = \App\Models\Subscriber::where('unsubscribe_token', $token)->first();
    
    if (!$subscriber) {
        return response()->view('errors.404', [], 404);
    }
    
    $subscriber->unsubscribe();
    
    return view('unsubscribe-success', compact('subscriber'));
})->name('unsubscribe');

Route::get('/email/open/{subscriber}', function ($subscriber) {
    $subscriber = \App\Models\Subscriber::find($subscriber);
    
    if (!$subscriber) {
        return response('', 404);
    }
    
    $sentEmail = \App\Models\SentEmail::where('subscriber_id', $subscriber->id)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($sentEmail) {
        \App\Models\EmailAnalytic::trackOpen($sentEmail, request()->userAgent(), request()->ip());
        $sentEmail->campaign->increment('opened_count');
    }
    
    return response()->file(public_path('images/pixel.png'), [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('email.open');

Route::get('/email/click/{sentEmail}', function ($sentEmail) {
    $sentEmail = \App\Models\SentEmail::find($sentEmail);
    
    if (!$sentEmail) {
        return response('', 404);
    }
    
    $url = request()->get('url');
    
    if ($url) {
        \App\Models\EmailAnalytic::trackClick($sentEmail, $url, request()->userAgent(), request()->ip());
        $sentEmail->campaign->increment('clicked_count');
    }
    
    return redirect($url ?: '/');
})->name('email.click');
