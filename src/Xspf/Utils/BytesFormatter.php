<?php

namespace Xspf\Utils;

class BytesFormatter
{
    /** @var int */
    private $bytes;

    /**
     * @param int $bytes
     *
     * @return string
     */
    public static function formatBytes(int $bytes): string
    {
        return (new self($bytes))->format();
    }

    public function __construct(int $bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $symbol = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $c = 0;
        $bytes = $this->bytes;
        while ($bytes >= 1024 && isset($symbol[$c + 1])) {
            $c++;
            $bytes = $bytes / 1024;
        }

        return number_format($bytes, (($c > 0) ? 2 : 0), ',', '.') . ' ' . $symbol[$c];
    }
}
