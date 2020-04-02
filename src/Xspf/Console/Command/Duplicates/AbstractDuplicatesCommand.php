<?php

namespace Xspf\Console\Command\Duplicates;

use ArrayObject;
use Closure;
use Exception;
use Generator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Console\Command\AbstractCommand;
use Xspf\File\File;
use Xspf\File\FileLocatorTrait;
use Xspf\Index\IndexModelFactory;

abstract class AbstractDuplicatesCommand extends AbstractCommand
{
    use FileLocatorTrait;

    const SUPPORTED_ALGORITHMS = [
        'md5',
        'sha1',
    ];

    const ACTIONS = [
        'path',
        'index',
        'playlist',
    ];

    protected function configure()
    {
        $this->setAliases([str_replace('duplicates:', 'duplicate:', $this->getName())])
            ->addArgument('action', InputArgument::REQUIRED, implode('|', self::ACTIONS))
            ->addArgument('value', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Value(s) for the action')
            ->addOption('algorithm', 'r', InputOption::VALUE_REQUIRED, implode('|', self::SUPPORTED_ALGORITHMS), current(self::SUPPORTED_ALGORITHMS))
            ->addOption('progress', 'p', InputOption::VALUE_NONE, 'Show progress');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array|ArrayObject
     *
     * @throws Exception
     */
    protected function getFilesByAction(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $values = (array)$input->getArgument('value');

        $files = new ArrayObject();
        switch ($action) {
            case 'path':
                foreach ($values as $fileOrFolder) {
                    foreach ($this->getFiles($fileOrFolder, $output) as $file) {
                        $files[] = $file;
                    }
                }
                break;

            case 'index':
                foreach ($values as $fileOrFolder) {
                    foreach ($this->getFiles($fileOrFolder, $output) as $indexFile) {
                        $indexModel = IndexModelFactory::factory($indexFile);
                        $indexModel->load(false, true);

                        foreach ($indexModel->getFiles() as $file) {
                            $files[] = $file;
                        }
                    }
                }
                break;

            case 'playlist':
                foreach ($values as $fileOrFolder) {
                    foreach ($this->getFiles($fileOrFolder, $output) as $playlistFile) {
                        $playlist = new File($playlistFile);
                        $playlist->load();

                        foreach ($playlist->getTracks() as $track) {
                            $files[] = $track->getLocation();
                        }
                    }
                }
                break;

            default:
                $output->writeln('<error>Invalid action:</error> ' . $action);
                $output->writeln('Supported actions:');
                foreach (self::ACTIONS as $action) {
                    $output->writeln('  - ' . $action);
                }
                throw new Exception('Invalid action');
        }

        $array = array_unique($files->getArrayCopy());

        return new ArrayObject(array_combine($array, $array));
    }

    /**
     * @param ArrayObject    $files
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return Generator
     */
    protected function getChecksums(ArrayObject $files, InputInterface $input, OutputInterface $output)
    {
        $algo = $this->getAlgo($input, $output);

        $progressOutput = ($input->getOption('progress')) ? $output : new NullOutput();
        $progressBar = new ProgressBar($progressOutput, $files->count());
        $progressBar->setFormat('debug');
        $progressBar->setRedrawFrequency(2);
        $progressBar->start();

        foreach ($files as $file) {
            $progressBar->advance();

            try {
                yield $file => $algo($file);
            } catch (Exception $e) {
            }
        }
        $progressBar->finish();
        $progressOutput->writeln('');
        $progressOutput->writeln('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return Closure|null
     */
    protected function getAlgo(InputInterface $input, OutputInterface $output)
    {
        $algo = $input->getOption('algorithm');

        switch ($algo) {
            case 'md5':
                $callback = function ($file) {
                    return md5_file($file);
                };
                break;

            case 'sha1':
                $callback = function ($file) {
                    return sha1_file($file);
                };
                break;

            default:
                $callback = function ($file) use ($algo) {
                    return hash_file($algo, $file);
                };
                break;
        }

        return $callback;
    }

    /**
     * @param string           $file
     * @param OutputInterface  $output
     * @param ArrayObject|null $checksums
     *
     * @return ArrayObject
     */
    protected function parseChecksumsFromFile($file, OutputInterface $output, ArrayObject $checksums = null)
    {
        $checksums = $checksums ?? new ArrayObject();
        foreach ($this->getFiles($file, $output) as $file) {
            foreach (file($file) as $line) {
                $result = explode(': ', $line);

                if (count($result) === 2) {
                    $checksums[trim($result[1])] = trim($result[0]);
                }
            }
        }

        return $checksums;
    }
}
