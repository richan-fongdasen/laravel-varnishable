<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Varnish hosts
    |--------------------------------------------------------------------------
    |
    | Specify the hostnames of your varnish instances. You can use array
    | to specify multiple varnish instances.
    |
    */
    'varnish_hosts' => env('VARNISH_HOST', '127.0.0.1'),

    /*
    |--------------------------------------------------------------------------
    | Varnish port
    |--------------------------------------------------------------------------
    |
    | Specify the port number that your varnish instances are listening to.
    |
    */
    'varnish_port' => env('VARNISH_PORT', 6081),

    /*
    |--------------------------------------------------------------------------
    | Cache duration
    |--------------------------------------------------------------------------
    |
    | Specify the default varnish cache duration in minutes.
    |
    */
    'cache_duration' => env('VARNISH_DURATION', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Cacheable header
    |--------------------------------------------------------------------------
    |
    | Specify the custom HTTP header that we should add, so Varnish can
    | recognize any responses containing the header and cache them.
    |
    */
    'cacheable_header' => 'X-Varnish-Cacheable',

    /*
    |--------------------------------------------------------------------------
    | Uncacheable header
    |--------------------------------------------------------------------------
    |
    | Specify the custom HTTP header that we should add, so Varnish won't
    | cache any reponses containing this header.
    |
    */
    'uncacheable_header' => 'X-Varnish-Uncacheable',

    /*
    |--------------------------------------------------------------------------
    | Use ETag Header
    |--------------------------------------------------------------------------
    |
    | Please specify if you want to use ETag header for any of your static 
    | contents.
    |
    */
    'use_etag' => true,

    /*
    |--------------------------------------------------------------------------
    | Use Last-Modified Header
    |--------------------------------------------------------------------------
    |
    | Please specify if you want to use Last-Modified header for any of your 
    | static contents.
    |
    */
    'use_last_modified' => true,

    /*
    |--------------------------------------------------------------------------
    | ESI capability header
    |--------------------------------------------------------------------------
    |
    | Please specify the ESI capability header that the varnish server would
    | send if there is any ESI support.
    |
    */
    'esi_capability_header' => 'Surrogate-Capability',

    /*
    |--------------------------------------------------------------------------
    | ESI reply header
    |--------------------------------------------------------------------------
    |
    | Please specify the HTTP header that you want to send as a reply
    | in response to ESI capability header which the varnish server sent in
    | current request.
    |
    */
    'esi_reply_header' => 'Surrogate-Control',

];
