<?php

namespace Synful\CLIParser\Commands\Util;

/**
 * Class used for all console commands.
 */
abstract class Command
{
    /**
     * The primary name of the command.
     *
     * @var string
     */
    public $name;

    /**
     * The alias for the command.
     *
     * @var string
     */
    public $alias;

    /**
     * The description for the command.
     *
     * @var string
     */
    public $description;

    /**
     * If set to true, the command will be required.
     *
     * @var bool
     */
    public $required;

    /**
     * The exec callback to be called when the command is run.
     *
     * @var Closure
     */
    public $exec;
}
