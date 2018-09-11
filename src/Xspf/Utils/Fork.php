<?php

namespace Xspf\Utils;

class Fork
{
    /**
     * @return bool|null
     */
    public function isForkingSupported()
    {
        static $isSupported = null;

        return ($isSupported === null)
            ? $isSupported = function_exists('pcntl_fork')
            : $isSupported;
    }

    /**
     * @param callable $child
     *
     * @return int
     *
     * @throws \Exception
     */
    public function fork($child)
    {
        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new \Exception('Fork failed!');
        } elseif ($pid > 0) {
            $result = call_user_func($child);

            if (is_int($result)) {
                exit($result);
            } elseif (is_bool($result)) {
                exit($result ? 0 : 1);
            } else {
                exit;
            }
        }

        return $pid;
    }

    /**
     * @return int
     */
    public function wait()
    {
        $status = 0;
        pcntl_wait($status);

        return $status;
    }
}
