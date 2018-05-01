<?php

namespace XspfMock;

use Xspf\WhiteAndBlacklistProviderTrait;

class WhiteAndBlacklistProviderTraitMock
{
    use WhiteAndBlacklistProviderTrait;

    public function __construct(array $blackList = [], array $whiteList = null)
    {
        $this->whiteList = $whiteList;
        $this->blackList = $blackList;
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
