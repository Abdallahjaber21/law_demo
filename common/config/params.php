<?php

return [
    'project-id' => 'mavenlb',
    'project-name' => 'Mavenlb',
    'project-short-name' => 'EM',
    'project-version' => '1.0.0',
    ////////////////////////////////////////////////////////////////////////////
    'adminEmail' => 'app@emaintain.com',
    'passwordResetEmail' => 'no-reply@emaintain.com',
    'noreplyEmail' => 'no-reply@emaintain.com',
    'supportEmail' => 'support@emaintain.com',
    'notificationEmail' => 'notification@emaintain.com',
    'user.passwordResetTokenExpire' => 3600,
    ////////////////////////////////////////////////////////////////////////////
    'open-route-token' => '5b3ce3597851110001cf624856b021f93d5749a790e786fc8d24774b',
    ////////////////////////////////////////////////////////////////////////////
    'googleMapsKey' => 'AIzaSyAKaYeUlQs83iir0S1AK5lncSlRIWcCidI',
    'pushNotificationKey' => 'AAAAJ2VUsFA:APA91bHJsco-oc9QeNjBjeaB_CILjdG2NSS2GiRIx_UZI1-RIdkq09EIWQyueLR_Wy0BY0rU5mDm3gOeluycenyK7PpHM1aAwg9AnUf3Ld4KoZQnvZ2QymaEaHVzmdqtHvCxU37E-WPE',
    'publicVapidKey' => 'BPRjrY0KynpVF7Ztbo-RFqdwsTZlG49Rc7uj-eIHZNGVtyoJ5O9tGRUohmUWen__COzuUCQe4kB00nB6PSRyblg',
    'firebaseConfig' => [
        "apiKey" => "AIzaSyAKaYeUlQs83iir0S1AK5lncSlRIWcCidI",
        "authDomain" => "fibrex.firebaseapp.com",
        "databaseURL" => "",
        "projectId" => "fibrex",
        "storageBucket" => "fibrex.appspot.com",
        "messagingSenderId" => "169203773520",
        "appId" => "1:169203773520:web:e77089256516fb58562155",
        "measurementId" => "G-4HSSPW7Q5Q",
    ],
    ////////////////////////////////////////////////////////////////////////////
    'languages' => [
        'en-US' => 'English',
        //        'ar-AR' => 'العربية',
    ],
    'languageRedirects' => [
        //        'en-US' => 'en',
        //        'ar-AR' => 'ar',
    ],
    'rtl-languages' => ['ar-AR'],

    'urlManagers' => require(__DIR__ . '/includes/_urlmanagers.php'),
    ////////////////////////////////////////////////////////////////////////////
];
