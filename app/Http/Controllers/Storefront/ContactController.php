<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\ContactRequest;
use App\Models\ContactMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(WhatsAppService $whatsApp): View
    {
        return view('storefront.contact', [
            'whatsappUrl' => $whatsApp->contactUrl($whatsApp->contactMessage()),
            'pageTitle' => __('storefront.contact.title').' | '.store_setting('store_name'),
            'metaDescription' => __('storefront.contact.meta_description'),
            'canonical' => route('contact'),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        if ($request->isHoneypotFilled()) {
            return back()->with('status', __('storefront.contact.sent'));
        }

        ContactMessage::query()->create($request->safe()->only(['name', 'email', 'phone', 'message']));

        return back()->with('status', __('storefront.contact.sent'));
    }
}
