<?php

namespace Xspf\Commands;

use Xspf\Utils;

abstract class AbstractCommand extends \DavaHome\Console\Command\AbstractCommand
{
    /**
     * @param string $description
     */
    protected function trackPerformance($description)
    {
        Utils::trackPerformance($this->getName(), $description);
    }
}
