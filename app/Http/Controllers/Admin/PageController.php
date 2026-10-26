<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\HtmlSanitizerService;
use App\Support\Slugger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly HtmlSanitizerService $sanitizer) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $pages = Page::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            }))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(config('store.pagination.admin', 15))
            ->withQueryString();

        return view('admin.pages.index', [
            'pages' => $pages,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Slugger::unique($data['title'], Page::class);
        $data['content'] = $this->sanitizer->clean($data['content']);

        $page = Page::query()->create($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', __('admin.pages.created'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $page->slug === 'about-us'
            ? 'about-us'
            : (filled($data['slug'] ?? null) ? $data['slug'] : Slugger::unique($data['title'], Page::class, $page->id));
        $data['content'] = $this->sanitizer->clean($data['content']);

        $page->update($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', __('admin.pages.updated'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->slug === 'about-us') {
            return back()->with('error', __('admin.pages.delete_blocked_about'));
        }

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('status', __('admin.pages.deleted'));
    }
}
