<?php

namespace Xspf\Commands\Duplicates;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListDuplicatesCommand extends AbstractDuplicatesCommand
{
    const SEPARATOR = ': ';

    protected function configure()
    {
        $this->setName('duplicates:list')
            ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input file (add missing checksums to file)', null)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file', '-');

        parent::configure();
    }

    protected function saveChecksums(\ArrayObject $checksumList, InputInterface $input, OutputInterface $output, $final = false)
    {
        if ($input->getOption('output') === '-') {
            return;
        }

        file_put_contents($input->getOption('output'), implode(PHP_EOL, $checksumList->getArrayCopy()) . PHP_EOL);

        if ($final) {
            $output->writeln('Saved <green>' . $checksumList->count() . '</green> files to <cyan>' . $input->getOption('output') . '</cyan>');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checksumList = new \ArrayObject();
        $files = $this->getFilesByAction($input, $output);

        if ($input->getOption('input') && file_exists($input->getOption('input'))) {
            $existingChecksums = new \ArrayObject();
            foreach (file($input->getOption('input')) as $line) {
                $result = explode(self::SEPARATOR, $line);
                if (count($result) === 2) {
                    $file = trim($result[1]);
                    $checksum = trim($result[0]);

                    $checksumList[$file] = $checksum . self::SEPARATOR . $file;
                    if (!empty($checksum)) {
                        $existingChecksums[$file] = $checksum;
                    }
                }
            }

            $missingFiles = new \ArrayObject();
            foreach ($files as $file) {
                if (!isset($existingChecksums[$file])) {
                    $missingFiles[] = $file;
                }
            }
            $files = $missingFiles;
        } else {
            // Fill checksum list with empty checksums
            foreach ($files as $file) {
                $checksumList[$file] = self::SEPARATOR . $file;
            }
        }
        $checksumList->ksort();
        $this->saveChecksums($checksumList, $input, $output);

        // Extend checksum list file by file
        $i = 0;
        foreach ($this->getChecksums($files, $input, $output) as $file => $checksum) {
            $checksumList[$file] = $checksum . self::SEPARATOR . $file;

            if ($i++ % 9 == 0) {
                $this->saveChecksums($checksumList, $input, $output);
            }
        }
        $checksumList->ksort();
        $this->saveChecksums($checksumList, $input, $output, true);
    }
}
