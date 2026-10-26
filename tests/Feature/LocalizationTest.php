<?php

use App\Services\InventoryService;
use Illuminate\Support\Facades\Lang;

test('every static Arabic translation key used by the application exists', function () {
    $missing = [];

    foreach ([resource_path('views'), app_path()] as $directory) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($files as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.php')) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            if (! preg_match_all('/__\(\s*\'((?:[^\'\\\\]|\\\\.)+)\'/', $contents, $matches)) {
                continue;
            }

            foreach ($matches[1] as $key) {
                if (str_contains($key, '$') || str_ends_with($key, '.') || str_ends_with($key, '_')) {
                    continue;
                }

                if (! Lang::has($key, 'ar')) {
                    $missing[] = $key.' in '.$file->getFilename();
                }
            }
        }
    }

    expect(array_unique($missing))->toBe([]);
});

test('all dynamic Arabic keys resolve for inventory modes reasons and settings fields', function () {
    foreach (InventoryService::MODES as $mode) {
        expect(Lang::has('admin.inventory.mode_'.$mode, 'ar'))->toBeTrue();
    }

    foreach (InventoryService::REASONS as $reason) {
        expect(Lang::has('admin.inventory.reason_'.$reason, 'ar'))->toBeTrue();
    }

    foreach (config('store.settings') as $group => $definition) {
        expect(Lang::has('admin.settings.'.$group, 'ar'))->toBeTrue();

        foreach (array_keys($definition['fields']) as $field) {
            expect(Lang::has('admin.settings.field_'.$field, 'ar'))->toBeTrue();
        }
    }

    expect(Lang::has('admin.analytics.source_product_page', 'ar'))->toBeTrue();
});

test('Arabic validation messages are available', function () {
    app()->setLocale('ar');

    expect(__('validation.required', ['attribute' => 'الاسم']))
        ->toBe('حقل الاسم مطلوب.')
        ->and(__('validation.email', ['attribute' => 'البريد']))
        ->not->toContain('validation.email');
});
