<?php

return [
    'brand' => [
        'name_ar' => 'لمسة أنوثة',
        'name_en' => 'Lamset Anotha',
        'whatsapp' => '96895426555',
        'instagram' => 'mkm13390',
    ],

    'legal' => [
        // اكتب البيانات الرسمية هنا مرة واحدة، وستظهر في الموقع والفاتورة.
        'business_name' => '',
        'commercial_registration' => '',
        'license_number' => '',
    ],

    'gift_cards' => [
        'minimum_amount' => 5.000,
        'maximum_amount' => 200.000,
        'quick_amounts' => [5.000, 10.000, 20.000, 30.000, 50.000],
        'allow_custom_amount' => true,
    ],

    'storefront' => [
        'default_language' => 'ar',
        'default_theme' => 'light',
        'default_currency' => 'OMR',
        'enable_guest_checkout' => true,
        'enable_buy_now' => true,
        'enable_mini_cart' => true,
        'enable_recently_viewed' => true,
        'enable_shop_the_look' => true,
        'enable_gift_finder' => true,
    ],

    'currencies' => [
        'OMR' => ['label_ar' => 'ريال عماني', 'label_en' => 'Omani Rial', 'flag' => '🇴🇲'],
        'AED' => ['label_ar' => 'درهم إماراتي', 'label_en' => 'UAE Dirham', 'flag' => '🇦🇪'],
        'SAR' => ['label_ar' => 'ريال سعودي', 'label_en' => 'Saudi Riyal', 'flag' => '🇸🇦'],
        'QAR' => ['label_ar' => 'ريال قطري', 'label_en' => 'Qatari Riyal', 'flag' => '🇶🇦'],
        'BHD' => ['label_ar' => 'دينار بحريني', 'label_en' => 'Bahraini Dinar', 'flag' => '🇧🇭'],
        'KWD' => ['label_ar' => 'دينار كويتي', 'label_en' => 'Kuwaiti Dinar', 'flag' => '🇰🇼'],
    ],
];
