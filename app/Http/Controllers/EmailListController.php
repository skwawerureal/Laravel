<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailList;
use App\Models\Subscriber;
use App\Jobs\ImportSubscribersFromExcel;

class EmailListController extends Controller
{
    public function index()
    {
        $emailLists = EmailList::withCount('subscribers')->orderBy('created_at', 'desc')->paginate(10);
        return view('email-lists.index', compact('emailLists'));
    }

    public function create()
    {
        return view('email-lists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_lists,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,archived'
        ]);

        EmailList::create($request->all());

        return redirect()->route('email-lists.index')
            ->with('success', 'Email list created successfully.');
    }

    public function show(EmailList $emailList)
    {
        $emailList->load(['subscribers' => function($query) {
            $query->orderBy('created_at', 'desc')->paginate(20);
        }]);
        return view('email-lists.show', compact('emailList'));
    }

    public function edit(EmailList $emailList)
    {
        return view('email-lists.edit', compact('emailList'));
    }

    public function update(Request $request, EmailList $emailList)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_lists,name,' . $emailList->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,archived'
        ]);

        $emailList->update($request->all());

        return redirect()->route('email-lists.index')
            ->with('success', 'Email list updated successfully.');
    }

    public function destroy(EmailList $emailList)
    {
        $emailList->delete();

        return redirect()->route('email-lists.index')
            ->with('success', 'Email list deleted successfully.');
    }

    public function import(Request $request, EmailList $emailList)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->store('imports');

        ImportSubscribersFromExcel::dispatch($emailList, $filePath);

        return redirect()->route('email-lists.show', $emailList)
            ->with('success', 'File uploaded and is being processed.');
    }

    public function addSubscriber(Request $request, EmailList $emailList)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'custom_fields' => 'nullable|array'
        ]);

        $subscriber = Subscriber::create($request->only([
            'email', 'first_name', 'last_name', 'custom_fields'
        ]));

        $emailList->subscribers()->attach($subscriber->id);
        $emailList->updateSubscriberCount();

        return redirect()->route('email-lists.show', $emailList)
            ->with('success', 'Subscriber added successfully.');
    }

    public function removeSubscriber(EmailList $emailList, Subscriber $subscriber)
    {
        $emailList->subscribers()->detach($subscriber->id);
        $emailList->updateSubscriberCount();

        return redirect()->route('email-lists.show', $emailList)
            ->with('success', 'Subscriber removed successfully.');
    }
}
