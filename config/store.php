<?php

use App\Support\Permission;

return [
    'locales' => [
        'ar' => ['name' => 'العربية', 'dir' => 'rtl'],
        'en' => ['name' => 'English', 'dir' => 'ltr'],
    ],

    'media_disk' => env('MEDIA_DISK', 'public'),

    'trusted_proxies' => env('TRUSTED_PROXIES'),

    'uploads' => [
        'image_max_kb' => 4096,
        'image_min_dimension' => 400,
        'image_max_dimension' => 6000,
        'image_widths' => [480, 960, 1920],
        'model_max_kb' => 20480,
    ],

    'orders' => [
        'max_quantity' => 20,
    ],

    'pagination' => [
        'products' => 12,
        'admin' => 15,
    ],

    'low_stock_default' => 5,

    'settings' => [
        'general' => [
            'permission' => Permission::MANAGE_SETTINGS,
            'fields' => [
                'store_name' => ['type' => 'text', 'default' => 'SQUAD'],
                'store_tagline' => ['type' => 'text', 'default' => 'أداء رياضي بمعايير الملعب'],
                'announcement_text' => ['type' => 'text', 'default' => 'تأكيد التوفر والتوصيل والدفع عند الاستلام عبر واتساب'],
                'show_announcement' => ['type' => 'toggle', 'default' => '1'],
                'whatsapp_number' => ['type' => 'text', 'default' => ''],
                'contact_phone' => ['type' => 'text', 'default' => ''],
                'contact_email' => ['type' => 'text', 'default' => ''],
                'address' => ['type' => 'textarea', 'default' => ''],
                'business_hours' => ['type' => 'text', 'default' => ''],
                'social_instagram' => ['type' => 'url', 'default' => ''],
                'social_tiktok' => ['type' => 'url', 'default' => ''],
                'social_x' => ['type' => 'url', 'default' => ''],
                'currency_code' => ['type' => 'text', 'default' => 'SAR'],
                'currency_symbol' => ['type' => 'text', 'default' => 'ر.س'],
                'logo' => ['type' => 'raw_image', 'default' => ''],
                'favicon' => ['type' => 'raw_image', 'default' => ''],
                'seo_title' => ['type' => 'text', 'default' => 'SQUAD | متجر المعدات الرياضية'],
                'seo_description' => ['type' => 'textarea', 'default' => 'أحذية رياضية، قمصان وأطقم تدريب وإكسسوارات مختارة بعناية. اطلب مباشرة عبر واتساب.'],
                'footer_note' => ['type' => 'text', 'default' => ''],
            ],
        ],

        'homepage' => [
            'permission' => Permission::MANAGE_CONTENT,
            'fields' => [
                'show_hero' => ['type' => 'toggle', 'default' => '1'],
                'hero_eyebrow' => ['type' => 'text', 'default' => 'NEW SEASON / PERFORMANCE'],
                'hero_title' => ['type' => 'text', 'default' => 'صُمم للحركة.'],
                'hero_title_highlight' => ['type' => 'text', 'default' => 'جاهز للملعب.'],
                'hero_subtitle' => ['type' => 'textarea', 'default' => 'أحذية، ملابس وأدوات رياضية مختارة لمَن يعرف قيمة الأداء. اطلب مقاسك ولونك مباشرة عبر واتساب.'],
                'hero_cta_label' => ['type' => 'text', 'default' => 'تسوّق المجموعة'],
                'hero_image' => ['type' => 'image', 'default' => ''],
                'hero_image_alt' => ['type' => 'text', 'default' => 'لاعب كرة قدم بالمعدات الرياضية'],
                'show_featured_categories' => ['type' => 'toggle', 'default' => '1'],
                'featured_categories_title' => ['type' => 'text', 'default' => 'تسوّق حسب الفئة'],
                'featured_categories_subtitle' => ['type' => 'text', 'default' => 'كل ما تحتاجه لموسمك الرياضي في مكان واحد.'],
                'show_featured_products' => ['type' => 'toggle', 'default' => '1'],
                'featured_products_title' => ['type' => 'text', 'default' => 'مختارات الملعب'],
                'show_new_arrivals' => ['type' => 'toggle', 'default' => '1'],
                'new_arrivals_title' => ['type' => 'text', 'default' => 'وصل حديثًا'],
                'show_offers' => ['type' => 'toggle', 'default' => '1'],
                'offers_title' => ['type' => 'text', 'default' => 'عروض الموسم'],
                'show_about' => ['type' => 'toggle', 'default' => '1'],
                'about_title' => ['type' => 'text', 'default' => 'معدات تليق بطموحك'],
                'about_short' => ['type' => 'textarea', 'default' => 'نختار لك معدات تدريب ومظهرًا رياضيًا يجمع بين الراحة والأداء. نهتم بالتفاصيل، من الخامة إلى المقاس، ونسهّل عليك الطلب والتواصل مباشرة مع فريق المتجر.'],
                'about_image' => ['type' => 'image', 'default' => ''],
                'show_why_us' => ['type' => 'toggle', 'default' => '1'],
                'why_title' => ['type' => 'text', 'default' => 'لماذا SQUAD؟'],
                'show_whatsapp_cta' => ['type' => 'toggle', 'default' => '1'],
                'whatsapp_cta_title' => ['type' => 'text', 'default' => 'محتاج مساعدة في اختيار مقاسك؟'],
                'whatsapp_cta_text' => ['type' => 'textarea', 'default' => 'فريقنا جاهز للإجابة عن أسئلتك وتأكيد التوفر وتفاصيل التوصيل عبر واتساب.'],
            ],
        ],
    ],
];
