<?php

use common\components\settings\Setting;

return [
    'contact_settings' => [
        'label' => 'Contact info',
        'description' => 'Global Contact Info Settings',
        'settings' => [
            'contact_email' => [
                'label' => 'Contact Email',
                'description' => 'Public email address for contact information',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => ""
            ],
            'contact_phone' => [
                'label' => 'Contact Phone',
                'description' => 'Public phone number for contact information',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => ""
            ],
            'contact_address_1' => [
                'label' => 'Contact Address Line 1',
                'description' => 'Public Line 1 address for contact information',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => ""
            ],
            'contact_address_2' => [
                'label' => 'Contact Address Line 2',
                'description' => 'Public Line 2 address for contact information',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => ""
            ],
        ]
    ],
    'user_settings' => [
        'label' => 'User Settings',
        'description' => 'Global Settings related to users',
        'settings' => [
            'max_login_attempts' => [
                'label' => 'Max login attempts',
                'description' => 'Number of allowed failed login attempts before user getting blocked',
                'type' => common\components\settings\Setting::TYPE_INTEGER,
                'default' => 5
            ],
            'user_lock_duration' => [
                'label' => 'Lock duration',
                'description' => 'Number of minutes user get blocked after many unsuccessfull logins',
                'type' => common\components\settings\Setting::TYPE_INTEGER,
                'default' => 10
            ],
        ]
    ],
    'mobile_app_settings' => [
        'label' => 'Mobile Apps',
        'description' => 'Global Mobile Apps settings',
        'settings' => [
            'android_version' => [
                'label' => 'Android Version',
                'description' => 'Latest Android version',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "1.0.0"
            ],
            'android_store' => [
                'label' => 'Android Store Link',
                'description' => 'Android Application Store page URL',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "https://play.google.com"
            ],
            'ios_version' => [
                'label' => 'iOS Version',
                'description' => 'Latest iOS version',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "1.0.0"
            ],
            'ios_store' => [
                'label' => 'iOS Store Link',
                'description' => 'iOS Application Store page URL',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "https://apps.apple.com/"
            ],
            'technician_android_version' => [
                'label' => 'Technician Android Version',
                'description' => 'Latest Android version',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "1.0.0"
            ],
            'technician_android_store' => [
                'label' => 'Technician Android Store Link',
                'description' => 'Android Application Store page URL',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "https://play.google.com"
            ],
            'technician_ios_version' => [
                'label' => 'Technician iOS Version',
                'description' => 'Latest iOS version',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "1.0.0"
            ],
            'technician_ios_store' => [
                'label' => 'Technician iOS Store Link',
                'description' => 'iOS Application Store page URL',
                'type' => common\components\settings\Setting::TYPE_STRING,
                'default' => "https://apps.apple.com/"
            ],
        ]
    ],
    'admin_settings' => [
        'label' => 'Admin settings',
        'description' => 'Global Settings related to admin dashboard',
        'settings' => [
            'admin-skin' => [
                'label' => 'Admin Skin',
                'description' => 'Admin dashboard colors',
                'type' => common\components\settings\Setting::TYPE_OPTION,
                'default' => 'skin-black',
                'config' => [
                    'options' => [
                        "skin-black" => 'White',
                        //                        "skin-blue" => 'Blue',
//                        "skin-red" => 'Red',
//                        "skin-yellow" => 'Yellow',
//                        "skin-purple" => 'Purple',
//                        "skin-green" => 'Green',
//                        "skin-black-light" => 'White-Light',
//                        "skin-blue-light" => 'Blue-Light',
//                        "skin-red-light" => 'Red-Light',
//                        "skin-yellow-light" => 'Yellow-Light',
//                        "skin-purple-light" => 'Purple-Light',
//                        "skin-green-light" => 'Green-Light'
                    ]
                ],
            ],
        ]
    ],
    ///////////////////////////////////////////////////////
    'system_settings' => [
        'label' => 'System Settings',
        'description' => 'Global system settings',
        'settings' => [
            //            'end_of_day_hour' => [
//                'label' => 'End of day hour',
//                'description' => 'What hour is considered the end of day',
//                'type' => common\components\settings\Setting::TYPE_INTEGER,
//                'default' => 17
//            ],

            'nearby_distance' => [
                'label' => 'Distance to allow checkin (meters)',
                'description' => 'Distance to consider locations nearby in meters',
                'type' => common\components\settings\Setting::TYPE_INTEGER,
                'default' => 50
            ],
        ]
    ],
];
