<?php

namespace Xspf\Console\Command\Duplicates;

use ArrayObject;
use Symfony\Component\Console\Helper\ProgressBar;
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
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Duplicate list result files (created by "duplicates:list")')
            ->addOption('no-progress', '', InputOption::VALUE_NONE, 'Hide progress');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePathList = (array)$input->getArgument('files');
        $files = new ArrayObject();
        foreach ($filePathList as $file) {
            $this->parseChecksumsFromFile($file, $output, $files);
        }
        if (count($filePathList) > 1) {
            $output->writeln(sprintf('Fetched checksums from %d duplicate list files', count($filePathList)));
        }

        $progressBar = new ProgressBar(($input->getOption('no-progress')) ? new NullOutput() : $output, $files->count());
        $progressBar->setRedrawFrequency(2);
        $progressBar->setFormat('debug');
        $progressBar->start();
        $checksums = new ArrayObject();
        $duplicates = new ArrayObject();

        $skipCount = 0;
        foreach ($files as $file => $checksum) {
            $progressBar->advance();

            // Skip empty checksums (maybe the list command crashed)
            if (empty($checksum)) {
                $skipCount++;
                $output->writeln(sprintf('<red>File "%s" has no checksum! Maybe duplicates:list crashed?</red>', $file), OutputInterface::VERBOSITY_DEBUG);
                continue;
            }

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

        if ($skipCount > 0) {
            $output->writeln(sprintf('<yellow>%d files were skipped!</yellow>', $skipCount));
        }

        if ($duplicates->count() <= 0) {
            $output->writeln('');
            $output->writeln('<green>No duplicates found!</green>');
            $output->writeln('');

            return 0;
        }

        $output->writeln('Duplicate files by checksum: ');
        foreach ($duplicates as $checksum => $files) {
            $output->writeln('');
            $output->writeln(sprintf('<cyan>Checksum: %s</cyan>', $checksum));
            foreach ($files as $file) {
                $output->writeln(sprintf('  - %s', $file));
            }
        }
        $output->writeln('');

        return 0;
    }
}
