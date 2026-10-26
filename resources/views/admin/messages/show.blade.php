<x-layouts.admin :title="__('admin.messages.show_title', ['name' => $message->name])">
    <x-admin.page-header :title="__('admin.messages.show_title', ['name' => $message->name])" :subtitle="__('admin.messages.received_at', ['date' => $message->created_at?->format('Y-m-d H:i')])">
        <x-slot:actions>
            <a href="{{ route('admin.messages.index') }}" class="btn-ghost btn-sm">
                <x-store.icon name="chevron-start" class="size-4" />
                {{ __('admin.messages.back') }}
            </a>
            <form method="POST" action="{{ route($message->read_at === null ? 'admin.messages.read' : 'admin.messages.unread', $message) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-outline btn-sm">
                    <x-store.icon name="mail" class="size-4" />
                    {{ $message->read_at === null ? __('admin.messages.mark_read') : __('admin.messages.mark_unread') }}
                </button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="surface-card overflow-hidden" aria-labelledby="message-body-title">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-full bg-smoke text-ink">
                        <x-store.icon name="message" class="size-5" />
                    </span>
                    <div>
                        <h2 id="message-body-title" class="text-sm font-black">{{ __('admin.messages.subject') }}</h2>
                        <p class="mt-0.5 text-[11px] text-muted">{{ $message->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
                @if ($message->read_at === null)
                    <span class="badge-danger">{{ __('admin.messages.unread') }}</span>
                @else
                    <span class="badge-muted">{{ __('admin.messages.read') }}</span>
                @endif
            </div>

            <div class="px-5 py-6 sm:px-6">
                <div class="prose-ar whitespace-pre-line text-[15px] leading-8">{{ $message->message }}</div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-smoke/40 px-5 py-4 sm:px-6">
                <p class="text-xs text-muted">{{ __('admin.messages.reply_hint') }}</p>
                <div class="flex items-center gap-2">
                    @if ($message->email)
                        <a href="mailto:{{ $message->email }}" class="btn-primary btn-sm">
                            <x-store.icon name="mail" class="size-4" />
                            {{ __('admin.messages.email') }}
                        </a>
                    @endif
                    @if ($message->phone)
                        <a href="tel:{{ $message->phone }}" class="btn-outline btn-sm">
                            <x-store.icon name="phone" class="size-4" />
                            {{ __('admin.messages.phone') }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <div class="space-y-6">
            <section class="surface-card p-5" aria-labelledby="message-contact-title">
                <h2 id="message-contact-title" class="text-base font-black">{{ __('admin.messages.contact_details') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ __('admin.common.name') }}</dt>
                        <dd class="mt-0.5 font-bold">{{ $message->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ __('admin.messages.email') }}</dt>
                        <dd class="mt-0.5 break-all">
                            @if ($message->email)
                                <a href="mailto:{{ $message->email }}" class="font-semibold hover:underline" dir="ltr">{{ $message->email }}</a>
                            @else
                                <span class="text-muted">{{ __('admin.messages.no_email') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ __('admin.messages.phone') }}</dt>
                        <dd class="mt-0.5">
                            @if ($message->phone)
                                <a href="tel:{{ $message->phone }}" class="font-semibold hover:underline" dir="ltr">{{ $message->phone }}</a>
                            @else
                                <span class="text-muted">{{ __('admin.common.none') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ __('admin.common.date') }}</dt>
                        <dd class="mt-0.5 text-xs">{{ $message->created_at?->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-danger/20 bg-danger-soft/60 p-5" aria-labelledby="message-danger-title">
                <h2 id="message-danger-title" class="text-sm font-black text-danger">{{ __('admin.common.danger_zone') }}</h2>
                <p class="mt-1 text-xs leading-5 text-danger/80">{{ __('admin.messages.delete_confirm') }}</p>
                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" class="mt-4" onsubmit="return confirm('{{ __('admin.messages.delete_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">
                        <x-store.icon name="trash" class="size-4" />
                        {{ __('admin.messages.delete') }}
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
