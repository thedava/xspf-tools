<?php

namespace Xspf\Console\Command\Duplicates;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Xspf\Utils\LocalFile;

class ShowDuplicatesCommand extends AbstractDuplicatesCommand
{
    /** @var bool */
    private $skipInteraction = false;

    /** @var bool */
    private $alwaysChooseFirst = false;

    /** @var array|string[] */
    private $deletedFilesList = [];

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
     * @param string $file
     *
     * @return string
     */
    private function getFileName(string $file): string
    {
        $localFile = new LocalFile($file);

        return sprintf(
            '%s (%s, %s)',
            $file,
            $localFile->sizeReadable(),
            $localFile->mtimeReadable()
        );
    }

    /**
     * @param OutputInterface $output
     * @param InputInterface  $input
     * @param array           $files
     */
    protected function handleInteractive(OutputInterface $output, InputInterface $input, array $files): void
    {
        if ($this->skipInteraction) {
            foreach ($files as $file) {
                $output->writeln(sprintf('  - %s', $this->getFileName($file)));
            }

            return;
        }

        $deletedFileCount = 0;
        foreach ($files as $file) {
            if (in_array($file, $this->deletedFilesList)) {
                $deletedFileCount++;
            }
        }
        if ((count($files) - $deletedFileCount) <= 1) {
            foreach ($files as $file) {
                if (in_array($file, $this->deletedFilesList)) {
                    $output->writeln(sprintf('  - [<green>ALREADY DELETED</green>] %s', $this->getFileName($file)));
                } else {
                    $output->writeln(sprintf('  - %s', $this->getFileName($file)));
                }
            }

            return;
        }

        $choices = [
            's' => '<yellow>Keep all and Skip all following (no delete)</yellow>',
            'k' => '<cyan>Keep all (no delete)</cyan>',
            'f' => '<green>Always use first match for all following</green>',
        ];
        $i = 0;
        $originalFileList = [];
        foreach ($files as $file) {
            if (!in_array($file, $this->deletedFilesList)) {
                $index = ++$i;
                $originalFileList[$index] = $file;
                $choices[$index] = $this->getFileName($file);
            }
        }
        $choiceQuestion = new ChoiceQuestion('Which file do you want to keep?', $choices, 'k');
        $originalValidator = $choiceQuestion->getValidator();
        $choiceQuestion->setValidator(function ($selected) use ($originalValidator, $choiceQuestion) {
            return empty($selected)
                ? $choiceQuestion->getDefault()
                : $originalValidator($selected);
        });

        $index = $this->alwaysChooseFirst
            ? 1
            : $this->getQuestionHelper()->ask($input, $output, $choiceQuestion);
        if (in_array($index, ['s', 'f', 'k']) || !array_key_exists($index, $choices) || intval($index) <= 0) {
            if ($index === 's') {
                $this->skipInteraction = true;
                $output->writeln('  - [<green>KEEP EVERYTHING</green> AND <cyan>SKIP FOLLOWING</cyan>]');
            } elseif ($index === 'f') {
                $this->alwaysChooseFirst = true;
                $output->writeln('  - [<green>ALWAYS CHOOSE FIRST OPTION</green> FOR <cyan>ALL FOLLOWING</cyan>]');
            } else {
                $output->writeln('  - [<green>KEEP EVERYTHING</green>]');
            }

            return;
        }
        $index = (int)$index;

        foreach ($originalFileList as $i => $file) {
            if ($index === $i) {
                $output->writeln(sprintf('  <green>- [KEEP] %s</green>', $file));
                continue;
            }

            if (in_array($file, $this->deletedFilesList) || (new LocalFile($file))->delete()) {
                $this->deletedFilesList[] = $file;
                $output->writeln(sprintf('  <red>- [DELETE] %s</red>', $file));
            } else {
                $output->writeln(sprintf('  <yellow>- [DELETE FAILED] %s</yellow>', $file));
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePathList = (array)$input->getArgument('files');
        $files = [];
        foreach ($filePathList as $file) {
            $this->parseChecksumsFromFile($file, $output, $files);
        }
        if (count($filePathList) > 1) {
            $output->writeln(sprintf('Fetched checksums from %d duplicate list files', count($filePathList)));
        }

        $duplicateChecksum = [];
        $duplicateFilename = [];
        $skipCount = $this->determineDuplicates($input, $output, $files, $duplicateChecksum, $duplicateFilename);

        if ($skipCount > 0) {
            $output->writeln(sprintf('<yellow>%d files were skipped!</yellow>', $skipCount));
        }

        if (count($duplicateChecksum) <= 0 && count($duplicateFilename) <= 0) {
            $output->writeln('');
            $output->writeln('<green>No duplicates found!</green>');
            $output->writeln('');

            return 0;
        }

        // Skip interaction if no --interactive was given
        $this->skipInteraction = !$input->getOption('interactive');

        // Display checksums
        if (count($duplicateChecksum) > 0) {
            $output->writeln('<green>Duplicate files by checksum:</green>');
            foreach ($duplicateChecksum as $checksum => $files) {
                $output->writeln('');
                $output->writeln(sprintf('<cyan>Checksum: %s</cyan>', $checksum));
                $this->handleInteractive($output, $input, $files);
                $output->writeln('');
            }
        }

        // Display filenames
        if (count($duplicateFilename) > 0) {
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
     * @param array     $files
     * @param array     $duplicateChecksum
     * @param array     $duplicateFilename
     *
     * @return int
     */
    protected function determineDuplicates(
        InputInterface $input,
        OutputInterface $output,
        array $files,
        array &$duplicateChecksum,
        array &$duplicateFilename
    )
    {
        $progressBar = new ProgressBar(($input->getOption('no-progress')) ? new NullOutput() : $output, count($files));
        $progressBar->setRedrawFrequency(2);
        $progressBar->setFormat('debug');
        $progressBar->start();

        $compareChecksum = [];
        $compareFilename = [];

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
