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
    private $blackList = [];

    protected function appendWhiteAndBlacklistOptions(Command $command)
    {
        $command
            ->addOption('whitelist', 'w', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Whitelisted file patterns (e.g. *.avi)', null)
            ->addOption('blacklist', 'b', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Blacklisted file patterns (e.g. *.db)', ['*.db', '.*']);
    }

    protected function parseWhiteAndBlacklist(InputInterface $input)
    {
        $whiteList = $input->getOption('whitelist');
        if (empty($whiteList)) {
            $whiteList = null;
        }
        $this->whiteList = $whiteList;

        $blackList = $input->getOption('blacklist');
        if (empty($blackList)) {
            $blackList = [];
        }
        $this->blackList = $blackList;
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
                if (fnmatch($pattern, $filePath, FNM_CASEFOLD)) {
                    $match = true;
                    break;
                }
            }

            if (!$match) {
                return false;
            }
        }

        // Match against blacklist
        foreach ($this->blackList as $pattern) {
            if (fnmatch($pattern, $filePath, FNM_CASEFOLD)) {
                return false;
            }
        }

        return true;
    }
}
