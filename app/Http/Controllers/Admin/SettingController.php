<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\StoreSetting;
use App\Services\MediaService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class SettingController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
        private readonly WhatsAppService $whatsApp,
    ) {}

    public function edit(Request $request): View
    {
        $user = $request->user();

        $groups = collect(config('store.settings', []))
            ->filter(fn (array $group) => $user->can($group['permission']))
            ->all();

        abort_if($groups === [], 403);

        return view('admin.settings.edit', [
            'groups' => $groups,
            'values' => StoreSetting::values(),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $group = $request->targetGroup();

        if ($group === null) {
            abort(403);
        }

        $fields = config('store.settings.'.$group.'.fields', []);
        $validated = $request->validated();

        $pending = [];
        $newFiles = [];
        $oldFiles = [];

        try {
            foreach ($fields as $key => $definition) {
                if ($definition['type'] === 'toggle') {
                    $pending[$key] = $request->boolean($key) ? '1' : '0';

                    continue;
                }

                if ($definition['type'] === 'image' || $definition['type'] === 'raw_image') {
                    if (! $request->hasFile($key)) {
                        continue;
                    }

                    $directory = $definition['type'] === 'image' ? 'settings' : 'branding';
                    $path = $definition['type'] === 'image'
                        ? $this->media->storeImage($request->file($key), $directory)
                        : $this->media->storeRawImage($request->file($key), $directory);

                    $newFiles[] = $path;
                    $oldFiles[$key] = StoreSetting::get($key);
                    $pending[$key] = $path;

                    continue;
                }

                $value = (string) ($validated[$key] ?? '');

                if ($definition['type'] !== 'textarea') {
                    $value = trim($value);
                }

                if ($key === 'whatsapp_number' && $value !== '') {
                    $value = (string) $this->whatsApp->normalize($value);
                }

                $pending[$key] = $value;
            }

            DB::transaction(function () use ($pending): void {
                foreach ($pending as $key => $value) {
                    StoreSetting::set($key, $value);
                }
            });
        } catch (RuntimeException) {
            foreach ($newFiles as $path) {
                $this->media->delete($path);
            }

            return back()->withInput()->withErrors(['image' => __('admin.products.image_upload_failed')]);
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->media->delete($path);
            }

            throw $exception;
        }

        foreach ($oldFiles as $key => $oldPath) {
            $newPath = $pending[$key] ?? null;

            if (filled($oldPath) && $oldPath !== $newPath) {
                $this->media->delete($oldPath);
            }
        }

        return redirect()
            ->to(route('admin.settings.edit').'#'.$group)
            ->with('status', __('admin.settings.saved'));
    }
}
