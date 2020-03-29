<?php

namespace Xspf\Console\Command\Duplicates;

use ArrayObject;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ShowDuplicatesCommand extends AbstractDuplicatesCommand
{
    protected function configure()
    {
        $this->setName('duplicates:show')
            ->setAliases(['duplicate:show'])
            ->addArgument('file', InputArgument::REQUIRED, 'Duplicate list result file (created by "duplicates:list")')
            ->addOption('no-progress', '', InputOption::VALUE_NONE, 'Hide progress');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->parseChecksumsFromFile($input->getArgument('file'), $output);

        $progressBar = new ProgressBar(($input->getOption('no-progress')) ? new NullOutput() : $output, $files->count());
        $progressBar->setRedrawFrequency(2);
        $progressBar->setFormat('debug');
        $progressBar->start();
        $checksums = new ArrayObject();
        $duplicates = new ArrayObject();
        foreach ($files as $file => $checksum) {
            $progressBar->advance();

            if (isset($checksums[$checksum])) {
                if (!isset($duplicates[$checksum])) {
                    $duplicates[$checksum] = [
                        $checksums[$checksum],
                    ];
                }

                $duplicates[$checksum][] = $file;
            } else {
                $checksums[$checksum] = $file;
            }
        }
        unset ($files, $checksums);
        $progressBar->finish();
        $output->writeln('');

        if ($duplicates->count() <= 0) {
            $output->writeln('');
            $output->writeln('<green>No duplicates found!</green>');
            $output->writeln('');

            return 0;
        }

        $table = new Table($output);
        $table->setHeaders(['Checksum', 'Files']);
        foreach ($duplicates as $checksum => $files) {
            $table->addRow([$checksum, implode(PHP_EOL, $files)]);
        }
        $output->writeln('');
        $table->render();
        $output->writeln('');

        return 0;
    }
}
