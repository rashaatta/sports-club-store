<x-layouts.admin :title="__('admin.messages.title')">
    <x-admin.page-header :title="__('admin.messages.title')" :subtitle="__('admin.messages.subtitle')">
        <x-slot:actions>
            <span class="badge-volt">{{ $unreadCount }} {{ __('admin.messages.unread') }}</span>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.messages.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_200px_auto]">
            <div>
                <label for="message-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="message-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.messages.search_placeholder') }}" class="field ps-10">
                </div>
            </div>
            <div>
                <label for="message-status" class="sr-only">{{ __('admin.messages.filter_status') }}</label>
                <select id="message-status" name="status" class="field min-h-11">
                    <option value="" @selected(blank($status))>{{ __('admin.messages.all') }}</option>
                    <option value="unread" @selected($status === 'unread')>{{ __('admin.messages.unread') }}</option>
                    <option value="read" @selected($status === 'read')>{{ __('admin.messages.read') }}</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.apply') }}</button>
                @if ($search !== '' || filled($status))
                    <a href="{{ route('admin.messages.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($messages->isEmpty())
            <x-admin.empty-state :title="__('admin.messages.empty')" icon="message" />
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[780px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.messages.table_from') }}</th>
                            <th>{{ __('admin.messages.table_message') }}</th>
                            <th>{{ __('admin.messages.table_received') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr @class(['transition hover:bg-smoke/40', 'bg-volt-soft/20' => $message->read_at === null])>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <span @class([
                                            'size-2 shrink-0 rounded-full',
                                            'bg-danger' => $message->read_at === null,
                                            'bg-line' => $message->read_at !== null,
                                        ])></span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.messages.show', $message) }}" @class(['block max-w-[200px] truncate text-sm hover:underline', 'font-black' => $message->read_at === null, 'font-semibold' => $message->read_at !== null])>
                                                {{ $message->name }}
                                            </a>
                                            <span class="mt-0.5 block max-w-[200px] truncate text-[11px] text-muted" dir="ltr">{{ $message->email ?: $message->phone }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="line-clamp-2 max-w-[360px] text-xs leading-5 text-muted">{{ $message->message }}</p>
                                </td>
                                <td class="whitespace-nowrap text-xs text-muted">
                                    {{ $message->created_at?->format('Y-m-d H:i') }}
                                    <span class="mt-0.5 block text-[10px]">{{ $message->created_at?->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn-ghost btn-sm">{{ __('admin.messages.view') }}</a>
                                        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('{{ __('admin.messages.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.messages.delete') }}">
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
            <div class="border-t border-line px-4 py-3">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
