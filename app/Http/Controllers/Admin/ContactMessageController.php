<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $messages = ContactMessage::query()
            ->when($status === 'unread', fn ($query) => $query->unread())
            ->when($status === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%');
            }))
            ->latest()
            ->paginate(config('store.pagination.admin', 15))
            ->withQueryString();

        return view('admin.messages.index', [
            'messages' => $messages,
            'status' => $status,
            'search' => $search,
            'unreadCount' => ContactMessage::query()->unread()->count(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', [
            'message' => $message,
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->read_at ?? now()]);

        return back()->with('status', __('admin.messages.marked_read'));
    }

    public function markUnread(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => null]);

        return back()->with('status', __('admin.messages.marked_unread'));
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('status', __('admin.messages.deleted'));
    }
}
