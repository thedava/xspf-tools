<?php

namespace Xspf\Console\Command\Duplicates;

use ArrayObject;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ShowDuplicatesCommand extends AbstractDuplicatesCommand
{
    protected function configure()
    {
        $this->setName('duplicates:show')
            ->setAliases(['duplicate:show'])
            ->setDescription('Show results of a duplicates file (created by "duplicates:list")')
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Duplicate list result files (created by "duplicates:list")')
            ->addOption('no-progress', '', InputOption::VALUE_NONE, 'Hide progress')
            ->addOption('checksum-only', 'c', InputOption::VALUE_NONE, 'Only compare by checksum')
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Handle duplicates interactively');
    }

    /**
     * @param OutputInterface $output
     * @param InputInterface  $input
     * @param array           $files
     */
    protected function handleInteractive(OutputInterface $output, InputInterface $input, array $files): void
    {
        if (!$input->getOption('interactive')) {
            foreach ($files as $file) {
                $output->writeln(sprintf('  - %s', $file));
            }

            return;
        }

        $i = 0;
        $keep = '<yellow>Keep all (no delete)</yellow>';
        $choices = [$i++ => $keep];
        foreach ($files as $file) {
            $choices[$i++] = $file;
        }
        $choiceQuestion = new ChoiceQuestion('Which file do you want to keep?', $choices);

        $selection = $this->getQuestionHelper()->ask($input, $output, $choiceQuestion);
        if ($selection === $keep || !in_array($selection, $choices)) {
            $output->writeln('  <green>- KEEP EVERYTHING</green>');

            return;
        }

        foreach ($files as $file) {
            if ($selection === $file) {
                $output->writeln(sprintf('  <green>- (KEEP) %s</green>', $file));
                continue;
            }

            $output->writeln(sprintf('  <red>- (DELETE) %s</red>', $file));
            unlink($file);
        }
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

        $duplicateChecksum = new ArrayObject();
        $duplicateFilename = new ArrayObject();
        $skipCount = $this->determineDuplicates($input, $output, $files, $duplicateChecksum, $duplicateFilename);

        if ($skipCount > 0) {
            $output->writeln(sprintf('<yellow>%d files were skipped!</yellow>', $skipCount));
        }

        if ($duplicateChecksum->count() <= 0 && $duplicateFilename->count() <= 0) {
            $output->writeln('');
            $output->writeln('<green>No duplicates found!</green>');
            $output->writeln('');

            return 0;
        }

        // Display checksums
        if ($duplicateChecksum->count() > 0) {
            $output->writeln('<green>Duplicate files by checksum:</green>');
            foreach ($duplicateChecksum as $checksum => $files) {
                $output->writeln('');
                $output->writeln(sprintf('<cyan>Checksum: %s</cyan>', $checksum));
                $this->handleInteractive($output, $input, $files);
                $output->writeln('');
            }
        }

        // Display filenames
        if ($duplicateFilename->count() > 0) {
            $output->writeln('<green>Duplicate files by filename:</green>');
            foreach ($duplicateFilename as $filename => $files) {
                $output->writeln('');
                $output->writeln(sprintf('<cyan>Filename: "%s"</cyan>', $filename));
                $this->handleInteractive($output, $input, $files);
                $output->writeln('');
            }
        }

        return 0;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ArrayObject     $files
     * @param ArrayObject     $duplicateChecksum
     * @param ArrayObject     $duplicateFilename
     *
     * @return int
     */
    protected function determineDuplicates(
        InputInterface $input,
        OutputInterface $output,
        ArrayObject $files,
        ArrayObject $duplicateChecksum,
        ArrayObject $duplicateFilename
    )
    {
        $progressBar = new ProgressBar(($input->getOption('no-progress')) ? new NullOutput() : $output, $files->count());
        $progressBar->setRedrawFrequency(2);
        $progressBar->setFormat('debug');
        $progressBar->start();

        $compareChecksum = new ArrayObject();
        $compareFilename = new ArrayObject();

        $onlyChecksum = $input->getOption('checksum-only');

        $skipCount = 0;
        foreach ($files as $file => $checksum) {
            $progressBar->advance();

            // Skip empty checksums (maybe the list command crashed)
            if (empty($checksum)) {
                $skipCount++;
                $output->writeln(sprintf('<red>File "%s" has no checksum! Maybe duplicates:list crashed?</red>', $file), OutputInterface::VERBOSITY_DEBUG);
            } else {
                // Checksum
                if (isset($compareChecksum[$checksum])) {
                    if (!isset($duplicateChecksum[$checksum])) {
                        $duplicateChecksum[$checksum] = [
                            $compareChecksum[$checksum],
                        ];
                    }

                    $duplicateChecksum[$checksum][] = $file;
                } else {
                    $compareChecksum[$checksum] = $file;
                }
            }

            // Filename
            if (!$onlyChecksum) {
                $filename = basename($file);
                if (isset($compareFilename[$filename])) {
                    if (!isset($duplicateFilename[$filename])) {
                        $duplicateFilename[$filename] = [
                            $compareFilename[$filename],
                        ];
                    }

                    $duplicateFilename[$filename][] = $file;
                } else {
                    $compareFilename[$filename] = $file;
                }
            }
        }
        $progressBar->finish();
        $output->writeln('');

        return $skipCount;
    }
}
