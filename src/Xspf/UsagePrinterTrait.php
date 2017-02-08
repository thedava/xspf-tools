<?php

namespace Xspf;


trait UsagePrinterTrait
{
    abstract public function getExecutedFileName();

    /**
     * @param array $args
     * @param bool  $printLabel
     * @param bool  $doubleEol
     */
    protected function printUsageCommand(array $args, $printLabel = true, $doubleEol = true)
    {
        $label = 'Usage: ';
        if (!$printLabel) {
            $label = str_repeat(' ', strlen($label));
        }

        $eol = PHP_EOL;
        if ($doubleEol) {
            $eol .= $eol;
        }

        echo $label, 'php ', $this->getExecutedFileName(), rtrim(' ' . implode(' ', $args)), $eol;
    }

    /**
     * @param string $text
     */
    protected function printDescription($text)
    {
        echo $text, PHP_EOL, PHP_EOL;
    }

    /**
     * @param string $label
     * @param array  $lines
     */
    protected function printUsageList($label, array $lines)
    {
        echo rtrim($label, ':'), ':', PHP_EOL;
        foreach ($lines as $line) {
            echo '    - ', $line, PHP_EOL;
        }
        echo PHP_EOL;
    }
}
