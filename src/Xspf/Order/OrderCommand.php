<?php

namespace Xspf\Order;

use Xspf\AbstractCommand;
use Xspf\File;

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

        $this->printDescription('Orders the given playlist using the given order type');
        $this->printUsageCommand(['order', '<order_type>', '<playlist_file>']);
        $this->printUsageList('Order Types', [
            'asc:    The file will be ordered by video file names in ascending order',
            'desc:   The file will be ordered by video file names in descending order',
            'random: The file will be ordered in random order',
        ]);
    }
}
