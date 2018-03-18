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
        \Synful\RequestHandlers\AdvancedEndpointsExample::class,
        \Synful\RequestHandlers\GetIPExample::class,
        \Synful\RequestHandlers\HttpCodeExample::class,
        \Synful\RequestHandlers\InputExample::class,
        \Synful\RequestHandlers\PrivateHandlerExample::class,
        \Synful\RequestHandlers\RequestTypeExample::class,
        \Synful\RequestHandlers\SecurityLevelExample::class,
        \Synful\RequestHandlers\SerializerExample::class,
        \Synful\RequestHandlers\SqlExample::class,
    ],
];
