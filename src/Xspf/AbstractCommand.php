<?php

namespace Xspf;

use Xspf\Create\CreateCommand;
use Xspf\Help\HelpCommand;
use Xspf\Order\OrderCommand;

abstract class AbstractCommand
{
    use UsagePrinterTrait;

    const COMMAND_HELP = 'help';

    /** @var array */
    protected static $arguments;

    /** @var array|static[] */
    protected static $commands = [];

    /** @var array */
    protected static $map = [
        self::COMMAND_HELP => HelpCommand::class,
        'order'            => OrderCommand::class,
        'create'           => CreateCommand::class,
    ];

    /** @var bool */
    protected $isHelpCommand = false;

    /**
     * @param array $argv
     */
    public static function setArguments($argv)
    {
        if (empty($argv) || !is_array($argv)) {
            $argv = [];

            // Check if phar archive
            $archive = \Phar::running(false);
            if ($archive === '') {
                $argv[0] = basename(__FILE__);
            } else {
                $argv[0] = basename($archive);
            }
        }

        self::$arguments = $argv;
    }

    /**
     * @param string $command
     *
     * @return $this
     * @throws \Exception
     */
    public static function factory($command)
    {
        if (isset(self::$commands[$command])) {
            return self::$commands[$command];
        }

        if (!isset(self::$map[$command])) {
            if (static::determineHelpArgs()) {
                $command = static::COMMAND_HELP;
            } else {
                throw new \Exception('Command "' . $command . '" not found!');
            }
        }

        self::$commands[$command] = new self::$map[$command]();
        return self::$commands[$command];
    }

    /**
     * @return mixed
     */
    public static function getCommandArg()
    {
        return (isset(self::$arguments[1]))
            ? self::$arguments[1]
            : static::COMMAND_HELP;
    }

    /**
     * @param boolean $isHelpCommand
     */
    public function setIsHelpCommand($isHelpCommand)
    {
        $this->isHelpCommand = $isHelpCommand;
    }

    /**
     * @return bool
     */
    protected static function determineHelpArgs()
    {
        foreach (['--help', '-h', '/h', '/?'] as $arg) {
            if (in_array($arg, self::$arguments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isHelpCommand()
    {
        return $this->isHelpCommand || static::determineHelpArgs();
    }

    public function getExecutedFileName()
    {
        return self::$arguments[0];
    }

    /**
     * @return void
     */
    abstract public function invoke();

    /**
     * Print out the usage of this command
     *
     * @param \Exception $error
     *
     * @return void
     */
    abstract public function printUsage(\Exception $error = null);
}
