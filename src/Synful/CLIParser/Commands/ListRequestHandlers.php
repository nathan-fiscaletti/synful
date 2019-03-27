<?php

namespace Synful\CLIParser\Commands;

use Synful\CLIParser\Commands\Util\Command;

class ListRequestHandlers extends Command
{
    /**
     * Construct the ListRequestHandlers command.
     */
    public function __construct()
    {
        $this->name = 'lrh';
        $this->description = 'List all RequestHandlers currently registered in the System.';
        $this->required = false;
        $this->alias = 'list-request-handlers';

        $this->exec = function () {
            sf_info(
                'You can register more RequestHandlers in '.
                sf_color('./config/RequestHandlers.php', 'light_green'),
                true
            );
            sf_info('', true);
            sf_info(sf_color('Registered Request Handlers', 'yellow'), true);
            sf_info('', true);
            foreach (sf_conf('requesthandlers.registered') as $requestHandlerClass) {
                sf_info(
                    sf_color('\\'.$requestHandlerClass, 'white').
                    sf_color('::', 'light_gray').
                    sf_color('class', 'light_blue'),
                    true
                );
            }

            return parameter_result_halt();
        };
    }
}
