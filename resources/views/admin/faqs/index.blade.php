<x-layouts.admin :title="__('admin.faqs.title')">
    <x-admin.page-header :title="__('admin.faqs.title')" :subtitle="__('admin.faqs.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.faqs.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.faqs.add') }}
            </a>
            <a href="{{ route('faq') }}" target="_blank" rel="noopener" class="btn-outline btn-sm">
                <x-store.icon name="external" class="size-4" />
                {{ __('admin.common.view_store') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card overflow-hidden">
        @if ($faqs->isEmpty())
            <x-admin.empty-state :title="__('admin.faqs.empty')" icon="faq">
                <a href="{{ route('admin.faqs.create') }}" class="btn-primary btn-sm">{{ __('admin.faqs.add') }}</a>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[720px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.faqs.table_question') }}</th>
                            <th>{{ __('admin.common.sort_order') }}</th>
                            <th>{{ __('admin.faqs.table_status') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $faq)
                            <tr class="transition hover:bg-smoke/40">
                                <td>
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="block max-w-[420px] truncate text-sm font-bold hover:underline">{{ $faq->question }}</a>
                                    <p class="mt-0.5 line-clamp-1 max-w-[420px] text-[11px] text-muted">{{ $faq->answer }}</p>
                                </td>
                                <td class="text-sm text-muted">{{ $faq->sort_order }}</td>
                                <td>
                                    @if ($faq->is_active)
                                        <span class="badge-success">{{ __('admin.common.active') }}</span>
                                    @else
                                        <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.edit') }}">
                                            <x-store.icon name="edit" class="size-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('{{ __('admin.faqs.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                                <x-store.icon name="trash" class="size-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
