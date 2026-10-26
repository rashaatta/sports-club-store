<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Database\Seeders\Concerns\ImportsSeedMedia;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    use ImportsSeedMedia;

    public function run(): void
    {
        $settings = [
            'store_name' => 'SQUAD',
            'store_tagline' => 'أداء رياضي بمعايير الملعب',
            'announcement_text' => 'تأكيد التوفر والتوصيل والدفع عند الاستلام عبر واتساب',
            'show_announcement' => '1',
            'footer_note' => 'الطلب والتأكيد والتوصيل تتم عبر واتساب. الأسعار تشمل ضريبة القيمة المضافة حيث ينطبق.',
        ];

        foreach ($settings as $key => $value) {
            StoreSetting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $whatsapp = trim((string) env('STORE_WHATSAPP_NUMBER', ''));

        if ($whatsapp !== '') {
            StoreSetting::query()->firstOrCreate(['key' => 'whatsapp_number'], ['value' => $whatsapp]);
        }

        foreach (['hero_image' => 'hero-track.webp', 'about_image' => 'about-gym.webp'] as $key => $file) {
            if (StoreSetting::get($key) === '') {
                StoreSetting::set($key, $this->importSeedImage($file, 'site'));
            }
        }
    }
}
