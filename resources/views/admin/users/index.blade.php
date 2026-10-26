<x-layouts.admin :title="__('admin.users.title')">
    <x-admin.page-header :title="__('admin.users.title')" :subtitle="__('admin.users.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.users.add') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label for="user-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="user-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.common.search_placeholder') }}" class="field ps-10">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.search') }}</button>
                @if ($search !== '')
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($users->isEmpty())
            <x-admin.empty-state :title="__('admin.users.empty')" icon="users">
                <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">{{ __('admin.users.add') }}</a>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[720px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.users.table_member') }}</th>
                            <th>{{ __('admin.users.table_role') }}</th>
                            <th>{{ __('admin.users.table_created') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="transition hover:bg-smoke/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-ink text-sm font-black text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="block max-w-[220px] truncate text-sm font-bold hover:underline">{{ $user->name }}</a>
                                                @if (auth()->id() === $user->id)
                                                    <span class="badge-volt">{{ __('admin.users.self_badge') }}</span>
                                                @endif
                                            </div>
                                            <span class="mt-0.5 block max-w-[260px] truncate text-[11px] text-muted" dir="ltr">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-outline">{{ $user->role?->label() }}</span></td>
                                <td class="text-xs text-muted">{{ $user->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.edit') }}">
                                            <x-store.icon name="edit" class="size-4" />
                                        </a>
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('admin.users.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                                    <x-store.icon name="trash" class="size-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
