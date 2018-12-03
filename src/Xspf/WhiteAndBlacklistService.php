<?php

namespace Xspf;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class WhiteAndBlacklistService
{
    /** @var bool */
    protected $hasWhiteList;

    /** @var string */
    protected $whiteListCached;

    /** @var bool */
    protected $hasBlackList;

    /** @var string */
    protected $blackListCached;

    /**
     * @param array $list
     *
     * @return string
     */
    private static function buildCacheString(array $list)
    {
        $cachedList = [];
        foreach ($list as $entry) {
            $cachedList[] = strtr(preg_quote($entry, '#'), [
                '\*' => '.*',
                '\?' => '.',
            ]);
        }

        return '#^(' . implode('|', $cachedList) . ')$#i';
    }

    /**
     * @param Command $command
     */
    public static function appendOptionsToCommand(Command $command)
    {
        $command
            ->addOption('whitelist', 'w', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Whitelisted file patterns (e.g. *.avi)', null)
            ->addOption('blacklist', 'b', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Blacklisted file patterns (e.g. *.db)');
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    public static function createFromCommandInput(InputInterface $input)
    {
        $whiteList = $input->getOption('whitelist');
        if (empty($whiteList) || !is_array($whiteList)) {
            $whiteList = null;
        }

        $blackList = $input->getOption('blacklist');
        if (empty($blackList) || !is_array($blackList)) {
            $blackList = null;
        }

        return new self($whiteList, $blackList);
    }

    /**
     * @param array|null $whiteList
     * @param array|null $blackList
     */
    public function __construct(array $whiteList = null, array $blackList = null)
    {
        if ($this->hasWhiteList = !empty($whiteList)) {
            $this->whiteListCached = self::buildCacheString($whiteList);
        }
        if ($this->hasBlackList = !empty($blackList)) {
            $this->blackListCached = self::buildCacheString($blackList);
        }
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function isFileAllowed($filePath)
    {
        // Does not match whiteList
        if ($this->hasWhiteList && preg_match($this->whiteListCached, $filePath) !== 1) {
            return false;
        }

        // Does match blackList
        if ($this->hasBlackList && preg_match($this->blackListCached, $filePath) === 1) {
            return false;
        }

        return true;
    }
}
