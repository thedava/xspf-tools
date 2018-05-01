<?php

namespace XspfMock;

use Symfony\Component\Console\Input\ArrayInput;
use Xspf\WhiteAndBlacklistProviderTrait;

class WhiteAndBlacklistProviderTraitMock
{
    use WhiteAndBlacklistProviderTrait;

    public function __construct(array $blackList = [], array $whiteList = null)
    {
        $input = new ArrayInput([
            'whitelist' => $whiteList,
            'blacklist' => $blackList,
        ]);
        $this->parseWhiteAndBlacklist($input);
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function checkFileAllowed($file)
    {
        return $this->isFileAllowed($file);
    }
}
