<?php

namespace Xspf;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait WhiteAndBlacklistProviderTrait
{
    /** @var null|array */
    private $whiteList = null;

    /** @var array */
    private $blackList = null;

    protected function appendWhiteAndBlacklistOptions(Command $command)
    {
        $command
            ->addOption('whitelist', 'w', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Whitelisted file patterns (e.g. *.avi)', null)
            ->addOption('blacklist', 'b', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Blacklisted file patterns (e.g. *.db)');
    }

    /**
     * @param InputInterface $input
     */
    protected function parseWhiteAndBlacklist(InputInterface $input)
    {
        $whiteList = $input->getOption('whitelist');
        if (empty($whiteList)) {
            $whiteList = null;
        }
        $this->whiteList = $whiteList;

        $blackList = $input->getOption('blacklist');
        if (empty($blackList)) {
            $blackList = null;
        }
        $this->blackList = $blackList;
    }

    /**
     * Match filename against a pattern
     *
     * @link http://php.net/manual/en/function.fnmatch.php
     *
     * @param string $pattern
     * @param string $string
     *
     * @return bool
     */
    private function match($pattern, $string)
    {
        static $match = null;

        if ($match === null) {
            $match = function_exists('fnmatch');
        }

        if ($match) {
            return fnmatch($pattern, $string, FNM_CASEFOLD);
        } else {
            $pattern = mb_strtolower($pattern);
            $string = mb_strtolower($string);

            return preg_match('#^' . strtr(preg_quote($pattern, '#'), ['\*' => '.*', '\?' => '.']) . '$#i', $string);
        }
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    protected function isFileAllowed($filePath)
    {
        // Match again whitelist first
        if ($this->whiteList !== null) {
            $match = false;
            foreach ($this->whiteList as $pattern) {
                if ($this->match($pattern, $filePath)) {
                    $match = true;
                    break;
                }
            }

            if (!$match) {
                return false;
            }
        }

        // Match against blacklist
        if ($this->blackList !== null) {
            foreach ($this->blackList as $pattern) {
                if ($this->match($pattern, $filePath)) {
                    return false;
                }
            }
        }

        return true;
    }
}
