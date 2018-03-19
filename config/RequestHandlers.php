<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Registered
     |--------------------------------------------------------------------------
     |
     | The RequestHandlers to register in the System.
     */

    'registered' => [
        \Synful\App\RequestHandlers\AdvancedEndpointsExample::class,
        \Synful\App\RequestHandlers\GetIPExample::class,
        \Synful\App\RequestHandlers\HttpCodeExample::class,
        \Synful\App\RequestHandlers\InputExample::class,
        \Synful\App\RequestHandlers\PrivateHandlerExample::class,
        \Synful\App\RequestHandlers\RequestTypeExample::class,
        \Synful\App\RequestHandlers\SecurityLevelExample::class,
        \Synful\App\RequestHandlers\SerializerExample::class,
    ],
];
