<?php

namespace Xspf\Console\Command\Duplicates;

use ArrayObject;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Utils;

class ListDuplicatesCommand extends AbstractDuplicatesCommand
{
    const SEPARATOR = ': ';

    protected function configure()
    {
        $this->setName('duplicates:list')
            ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input file (add missing checksums to file)', null)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file', '-')
            ->addOption('remove-missing', 'm', InputOption::VALUE_NONE, 'Remove missing files (only works if input file was given)');

        parent::configure();
    }

    protected function saveChecksums(ArrayObject $checksumList, InputInterface $input, OutputInterface $output, $final = false)
    {
        if ($input->getOption('output') === '-') {
            return;
        }

        file_put_contents($input->getOption('output'), implode(PHP_EOL, $checksumList->getArrayCopy()) . PHP_EOL);

        if ($final) {
            $output->writeln('Saved <green>' . $checksumList->count() . '</green> files to <cyan>' . $input->getOption('output') . '</cyan>');
        }
    }

    /**
     * @param ArrayObject     $files
     * @param ArrayObject     $checksumList
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function parseInputFile(ArrayObject $files, ArrayObject $checksumList, InputInterface $input, OutputInterface $output): bool
    {
        $inputFile = $input->getOption('input');
        if (!$inputFile) {
            return true;
        }

        if (!file_exists($inputFile)) {
            $otherInputFile = Utils::determinePath($inputFile);
            if (!file_exists($otherInputFile)) {
                $output->writeln(sprintf('<red>Input file "%s" not found! Skipping import of pre-calculated checksums!</red>', $inputFile));

                return false;
            }
            $inputFile = $otherInputFile;
        }

        $removeMissing = $input->getOption('remove-missing');
        if ($removeMissing) {
            $output->writeln('', OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<yellow>Will remove missing files from input file</yellow>', OutputInterface::VERBOSITY_VERBOSE);
        }

        $removeCount = 0;
        $existingCount = 0;
        foreach ($this->parseChecksumsFromFile($inputFile, $output) as $file => $checksum) {
            if ($removeMissing && !file_exists($file)) {
                $removeCount++;
                $output->writeln(sprintf('<red>Removing file "%s" because it was not found!</red>', $file), OutputInterface::VERBOSITY_DEBUG);
                continue;
            }

            $checksumList[$file] = $checksum . self::SEPARATOR . $file;
            if (!empty($checksum)) {
                $existingCount++;

                if (isset($files[$file])) {
                    unset($files[$file]);
                }
            }
        }

        if ($removeMissing) {
            if ($removeCount > 0) {
                $output->writeln(sprintf('<yellow>%d files were removed because they were missing</yellow>', $removeCount));
                $output->writeln('');
            } else {
                $output->writeln('<green>There are no missing files</green>', OutputInterface::VERBOSITY_VERBOSE);
                $output->writeln('', OutputInterface::VERBOSITY_VERBOSE);
            }
        }

        if ($existingCount > 0) {
            $output->writeln(sprintf('<cyan>Skipped %d files because there are already checksums for those.</cyan>', $existingCount));
            $output->writeln('');
        }

        return true;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checksumList = new ArrayObject();
        $files = $this->getFilesByAction($input, $output);
        $output->writeln(sprintf('Found %s files in given location', $files->count()));

        if (!$this->parseInputFile($files, $checksumList, $input, $output)) {
            return 1;
        }

        // Fill checksum list with empty values
        foreach ($files as $file) {
            $checksumList[$file] = self::SEPARATOR . $file;
        }
        $checksumList->ksort();
        $this->saveChecksums($checksumList, $input, $output);

        // Extend checksum list file by file
        $i = 0;
        foreach ($this->getChecksums($files, $input, $output) as $file => $checksum) {
            $checksumList[$file] = $checksum . self::SEPARATOR . $file;

            if ($i++ % 10 == 0) {
                $this->saveChecksums($checksumList, $input, $output);
            }
        }
        $checksumList->ksort();
        $this->saveChecksums($checksumList, $input, $output, true);

        return 0;
    }
}
