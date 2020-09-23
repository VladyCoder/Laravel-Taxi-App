<?php

return array(

    'IOSUser'     => array(
        'environment' => env('IOS_USER_ENV', 'development'),
        'certificate' => app_path().'/apns/user/tranxit_user_live.pem',
        'passPhrase'  => env('IOS_USER_PUSH_PASS', 'appoets123$'),
        'service'     => 'apns'
    ),
    'IOSProvider' => array(
        'environment' => env('IOS_PROVIDER_ENV', 'development'),
        'certificate' => app_path().'/apns/provider/tranxit_provider_live.pem',
        'passPhrase'  => env('IOS_PROVIDER_PUSH_PASS', 'appoets123$'),
        'service'     => 'apns'
    ),
    'Android' => array(
        'environment' => env('ANDROID_ENV', 'production'),
        'apiKey'      => env('ANDROID_PUSH_KEY', 'AAAAUv3z-n4:APA91bF2PWTdU573wW5TcevURV7o79rzwDtc5lewmeZsrCimFNKGaljQB3tNMUP4NlXEMS_YHvOiKrBV_y28FCvBA7EkecNTnVFmb-iloCuHKapwnlGpQhxLlIIaAbj_Igl18P9bgMMd'),
        'service'     => 'fcm'
    ),
);