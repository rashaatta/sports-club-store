<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <section class="surface-card p-5 sm:p-6" aria-labelledby="faq-content-title">
        <div class="mb-5 flex items-center gap-3">
            <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                <x-store.icon name="faq" class="size-4" />
            </span>
            <h2 id="faq-content-title" class="text-base font-black">{{ __('admin.faqs.title') }}</h2>
        </div>

        <div class="grid gap-4">
            <x-admin.field name="question" :label="__('admin.faqs.field_question')" :value="$faq?->question" maxlength="255" required />
            <x-admin.field
                name="answer"
                type="textarea"
                :label="__('admin.faqs.field_answer')"
                :value="$faq?->answer"
                :help="__('admin.faqs.answer_hint')"
                rows="8"
                maxlength="5000"
                required
            />
        </div>
    </section>

    <div class="space-y-6">
        <section class="surface-card p-5" aria-labelledby="faq-status-title">
            <h2 id="faq-status-title" class="text-base font-black">{{ __('admin.common.status') }}</h2>
            <div class="mt-4">
                <x-admin.toggle name="is_active" :label="__('admin.faqs.is_active')" :checked="$faq?->is_active ?? true" />
            </div>
            <div class="mt-4">
                <x-admin.field
                    name="sort_order"
                    type="number"
                    :label="__('admin.common.sort_order')"
                    :value="$faq?->sort_order ?? 0"
                    :help="__('admin.faqs.sort_hint')"
                    min="0"
                    step="1"
                    required
                />
            </div>
            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="faq-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>
    </div>
</div>
