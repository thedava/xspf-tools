<?php

namespace Xspf\Index;

interface IndexModelInterface
{
    const EXT_PLAIN = 'xd';
    const EXT_COMPRESSED = 'xdc';

    /**
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function getIndexFile();
}
