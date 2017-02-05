<?php

namespace Xspf\Order;

use Xspf\AbstractCommand;
use Xspf\File;
use Xspf\Utils;

class OrderCommand extends AbstractCommand
{
    /**
     * @return string|null
     */
    protected function determineOrderType()
    {
        return (isset(self::$arguments[2]) && in_array(self::$arguments[2], AbstractOrderType::getOrderTypes()))
            ? self::$arguments[2] : null;
    }

    /**
     * @return string|null
     */
    protected function determineFileName()
    {
        return (isset(self::$arguments[3]) && file_exists(self::$arguments[3]) && filesize(self::$arguments[3]) > 32)
            ? self::$arguments[3] : null;
    }

    public function invoke()
    {
        $order = AbstractOrderType::factory($this->determineOrderType());
        $file = new File($this->determineFileName());
        $file->load();
        $order->order($file);
        $file->save();
    }

    public function printUsage(\Exception $error = null)
    {
        echo 'Version ', Utils::getVersion(), PHP_EOL, PHP_EOL;

        if (!$this->isHelpCommand()) {
            echo 'Following error(s) occurred: ', PHP_EOL;
            if ($this->determineOrderType() === null) {
                echo 'Unknown or invalid order type given!', PHP_EOL;
            }
            if ($this->determineFileName() === null) {
                echo 'The given file does not exist or is empty!', PHP_EOL;
            }
            echo PHP_EOL;
        }

        echo 'Usage: php ', $this->getExecutedFileName(), ' order <order_type> <playlist_file>', PHP_EOL, PHP_EOL;

        echo 'Order Types:', PHP_EOL;
        echo '    asc:    The file will be ordered by video file names in ascending order', PHP_EOL;
        echo '    desc:   The file will be ordered by video file names in descending order', PHP_EOL;
        echo '    random: The file will be ordered in random order', PHP_EOL;

        echo PHP_EOL;
    }
}
