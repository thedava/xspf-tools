<?php

namespace Xspf;

use Xspf\Help\HelpCommand;
use Xspf\Order\OrderCommand;

abstract class AbstractCommand
{
    const COMMAND_HELP = 'help';

    /** @var array */
    protected static $arguments;

    /** @var array */
    protected static $map = [
        self::COMMAND_HELP => HelpCommand::class,
        'order'            => OrderCommand::class,
    ];

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
        if (!isset(self::$map[$command])) {
            throw new \Exception('Command "' . $command . '" not found!');
        }

        /** @var $this $cmdObj */
        $cmdObj = new self::$map[$command]();
        return $cmdObj;
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
     * @return bool
     */
    public function isHelpCommand()
    {
        return count(self::$arguments) <= 1 || isset(self::$arguments[1]) && in_array(self::$arguments[1], [
                '--help', '-h', '/h', '/?', 'help',
            ]);
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
