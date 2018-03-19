<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Commands
     |--------------------------------------------------------------------------
     |
     | The CLI Commands to register with the System.
     */

    'commands' => [
        \Synful\Util\CLIParser\Commands\Color::class,
        \Synful\Util\CLIParser\Commands\CreateHandler::class,
        \Synful\Util\CLIParser\Commands\CreateKey::class,
        \Synful\Util\CLIParser\Commands\CreateMiddleWare::class,
        \Synful\Util\CLIParser\Commands\CreateMigration::class,
        \Synful\Util\CLIParser\Commands\CreateModel::class,
        \Synful\Util\CLIParser\Commands\CreateSerializer::class,
        \Synful\Util\CLIParser\Commands\DisableKey::class,
        \Synful\Util\CLIParser\Commands\EnableKey::class,
        \Synful\Util\CLIParser\Commands\FirewallIp::class,
        \Synful\Util\CLIParser\Commands\Help::class,
        \Synful\Util\CLIParser\Commands\HideConfigChanges::class,
        \Synful\Util\CLIParser\Commands\ListKeys::class,
        \Synful\Util\CLIParser\Commands\ListRequestHandlers::class,
        \Synful\Util\CLIParser\Commands\ListSql::class,
        \Synful\Util\CLIParser\Commands\Migrate::class,
        \Synful\Util\CLIParser\Commands\Output::class,
        \Synful\Util\CLIParser\Commands\RemoveKey::class,
        \Synful\Util\CLIParser\Commands\ShowFirewall::class,
        \Synful\Util\CLIParser\Commands\TestAuth::class,
        \Synful\Util\CLIParser\Commands\UnfirewallIp::class,
        \Synful\Util\CLIParser\Commands\UpdateKey::class,
        \Synful\Util\CLIParser\Commands\WhitelistOnly::class,
    ],
];
