<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $messages = $query->latest()->paginate(20)->withQueryString();
        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read', 'is_read' => true]);
        }
        return view('admin.contact-messages.show', compact('message'));
    }

    public function updateStatus(Request $request, ContactMessage $message)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied'
        ]);

        $message->update([
            'status' => $request->status,
            'is_read' => $request->status !== 'new'
        ]);

        return back()->with('success', 'Status updated successfully');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('contact-messages.index')->with('success', 'Message deleted successfully');
    }
}
