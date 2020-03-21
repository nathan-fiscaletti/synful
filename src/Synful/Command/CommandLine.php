<?php

namespace Synful\Command;

use Ansi\Color16;
use ParameterParser\ParameterParser;
use ParameterParser\ParameterClosure;
use ParameterParser\ParameterCluster;
use Synful\Ansi\StringBuilder;

/**
 * Class used for parsing command line.
 */
class CommandLine
{
    /**
     * The ParameterCluster that contains the ParameterClosures.
     *
     * @var ParameterCluster
     */
    private $parameters;

    /**
     * Parse the command line and return the results.
     *
     * @param  array $argv
     * @return array
     */
    public function parse($argv)
    {
        $this->loadParameters();
        $parameterParser = new ParameterParser($argv, $this->parameters);

        $parameterParser->setErrorHandler(function (ParameterClosure $parameter, $errorMessage) {
            sf_error($errorMessage, true, false);
            sf_error('Usage: '.$parameter->getUsage(), true, false);
            sf_error('Check `-help` for more information.');
            exit;
        });

        $results = $parameterParser->parse();

        if ($parameterParser->haltedBy() != null) {
            exit;
        }

        if (! $parameterParser->isValid()) {
            $this->printUsage();
            exit;
        }

        return $results;
    }

    /**
     * Load the parameters from Util\Command\Commands
     * into the ParameterCluster.
     */
    public function loadParameters()
    {
        $this->parameters = new ParameterCluster();

        foreach (sf_conf('commandline.commands') as $commandClass) {
            $parameter = new $commandClass();
            $pc = parameter(
                '-',
                $parameter->name,
                $parameter->exec,
                $parameter->required
            );
            $pc->setDescription($parameter->description);
            $pc->addAlias($parameter->alias, '-');
            $this->parameters->add($pc);
        }

        $this->parameters->setDefault(function ($parameter) {
            return -1;
        });
    }

    /**
     * Print the usage to the console.
     */
    public function printUsage()
    {
        sf_error('Usage: ./synful [OPTION]...', true, false);
        sf_error('', true, false);
        sf_info('Options: ', true, false);
        $sb = new StringBuilder();
        foreach ($this->parameters->prefixes['-'] as $parameter) {
            if (! $parameter->isParent()) {
                sf_info('', true, false);
                sf_info(
                    $sb->empty()->bold()->underline()->color16(
                        Color16::FG_LIGHT_BLUE,
                        $parameter->getUsage()
                    ),
                    true,
                    false
                );
                $desc = $sb->empty()->bold()->color16(
                    Color16::FG_LIGHT_BLUE,
                    "╘═════ "
                )->resetBold()->color16(
                    Color16::FG_WHITE,
                    $parameter->description
                );
                sf_info($desc, true, false);
            }
        }
    }
}
