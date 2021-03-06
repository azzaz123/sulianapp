<?php

if (env('APP_Framework',false) == 'platform') {
    $attachment = 'static/upload';
} else {
    $attachment = '../../attachment';
}


return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'syst_images' => [
            'driver' => 'local',
            'root' => base_path('static/upload/images/0/'.date('Y').'/'.date('m')),
            'url' => '/static/upload/images/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
        ],

        'newimages' => [
            'driver' => 'local',
            'root' => base_path($attachment . '/newimage'),
            'url' => '/newimage',
            'visibility' => 'public',
        ],

        'videos' => [
            'driver' => 'local',
            'root' => base_path('static/upload/videos/0/'.date('Y').'/'.date('m')),
            'url' => '/static/upload/videos/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
        ],

        'audios' => [
            'driver' => 'local',
            'root' => base_path('static/upload/audios/0/'.date('Y').'/'.date('m')),
            'url' => '/static/upload/audios/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'avatar' => [
            'driver' => 'local',
            'root' => base_path($attachment .'/avatar'),
            'url' => env('APP_URL').'/attachment/avatar',
            'visibility' => 'public',
        ],

        'image' => [
            'driver' => 'local',
            'root' => base_path($attachment . '/image'),
            'url' => 'image',
            'visibility' => 'public',
        ],

        // ???????????? ???????????????????????? ??????????????????
        'photoimage' => [
            'driver' => 'local',
            'root' => base_path($attachment . '/photoimage'),
            'url' => env('APP_URL').'photoimage',
            'visibility' => 'public',
        ],


        'cert' => [
            'driver' => 'local',
            'root' => storage_path('cert'),
        ],
        // ??????????????????excel??????????????????
        'recharge' => [
            'driver' => 'local',
            'root' => storage_path('app/public/recharge'),
        ],

        // ??????????????????excel??????????????????
        'orderexcel' => [
            'driver' => 'local',
            'root' => storage_path('app/public/orderexcel'),
        ],

        // ??????????????????excel??????????????????
        'virtualcard' => [
            'driver' => 'local',
            'root' => storage_path('app/public/virtualcard'),
        ],

        // ????????? ????????????excel??????????????????
        'netcar' => [
            'driver' => 'local',
            'root' => storage_path('app/public/netcar'),
        ],

        
        // ????????????????????????
        'yop' => [
            'driver' => 'local',
            'root' => storage_path('app/public/yop'),
            'url' => env('APP_URL').'/storage/public/yop',
        ],

        // ????????????????????????
        'business_card' => [
            'driver' => 'local',
            'root' => storage_path('app/public/business_card'),
            'url' => env('APP_URL').'/storage/public/business_card',
        ],

        //?????????????????????????????????
        'dragon_deposit' => [
            'driver' => 'local',
            'root' => storage_path('app/dragon-deposit'),
            'url' => env('APP_URL').'/storage/app/dragon-deposit',
        ],

        'upload' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatar'),
            'url' => env('APP_URL').'/storage/public/avatar',
            'visibility' => 'public',
        ],

        'banner' => [
            'driver' => 'local',
            'root' => storage_path('app/public/banner'),
            'url' => env('APP_URL').'/storage/public/banner',
            'visibility' => 'public',
        ],

        //??????CSV??????
        'taobaoCSV' => [
            'driver' => 'local',
            'root'=> base_path('plugins/goods-assistant/storage/examples'),
            'url' => env('APP_URL').'plugins/goods-assistant/storage/examples',
            'visibility' => 'public',
        ],

        //??????CSV??????
        'taobaoCSVupload' => [
            'driver' => 'local',
            'root'=> base_path('plugins/goods-assistant/storage/upload'),
            'url' => env('APP_URL').'plugins/goods-assistant/storage/upload',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
    ],
];
