<?php

namespace Synful\Util\CLIParser;

use ParameterParser\ParameterParser;
use ParameterParser\ParameterClosure;
use ParameterParser\ParameterCluster;

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
            sf_error($errorMessage, true, false, false);
            sf_error('Usage: '.$parameter->getUsage(), true, false, false);
            sf_error('Check `-help` for more information.');
            exit;
        });

        $results = $parameterParser->parse();

        if (! $parameterParser->isValid()) {
            $this->printUsage();
            exit;
        }

        return $results;
    }

    /**
     * Load the parameters from Util\CLIParser\Commands
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
        sf_error('Usage: ./synful [options]', true, false, false);
        sf_error('', true, false, false);
        sf_info('Options: ', true, false, false);
        foreach ($this->parameters->prefixes['-'] as $parameter) {
            if (! $parameter->isParent()) {
                sf_info('', true, false, false);
                sf_info(sf_color($parameter->getUsage(), 'light_blue'), true, false, false);
                sf_info($parameter->description, true, false, false);
            }
        }
    }
}