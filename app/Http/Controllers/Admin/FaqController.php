<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faqs.index', [
            'faqs' => Faq::query()->ordered()->paginate(config('store.pagination.admin', 15)),
        ]);
    }

    public function create(): View
    {
        return view('admin.faqs.create');
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::query()->create($request->validated());

        return redirect()
            ->route('admin.faqs.index')
            ->with('status', __('admin.faqs.created'));
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', [
            'faq' => $faq,
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()
            ->route('admin.faqs.edit', $faq)
            ->with('status', __('admin.faqs.updated'));
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('status', __('admin.faqs.deleted'));
    }
}
