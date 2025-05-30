<?php

namespace Xspf\Console\Command\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Console\Command\AbstractCommand;
use Xspf\File\FileLocatorTrait;
use Xspf\Index\IndexModel;
use Xspf\Index\IndexModelFactory;
use Xspf\WhiteAndBlacklistService;

class CreateIndexCommand extends AbstractCommand
{
    use FileLocatorTrait;

    protected function configure()
    {
        $this->setName('index:create')
            ->setDescription('Create an index file')
            ->setHelp(implode(PHP_EOL, [
                'This command creates an index file used by various other xspf-tools commands',
                '',
                'Index files can be used to dynamically create playlist files (e.g. if you use ',
                'Windows and Linux the paths of the files may be inconsistent. The index file ',
                'stores all paths relative to provide cross-system-support)',
            ]))
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The path of the file', 'index.' . IndexModel::EXT_COMPRESSED)
            ->addOption('absolute', 'a', InputOption::VALUE_NONE, 'Keep absolute paths');
        WhiteAndBlacklistService::appendOptionsToCommand($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Creating index. This may take a while...');
        $whiteAndBlacklistService = WhiteAndBlacklistService::createFromCommandInput($input);

        $indexModel = IndexModelFactory::factory($input->getOption('output'));
        foreach ((array)$input->getArgument('file-or-folder') as $fileOrFolder) {
            foreach ($this->getFiles($fileOrFolder, $output) as $file) {
                if ($whiteAndBlacklistService->isFileAllowed($file)) {
                    $output->writeln('Adding ' . $file, $output::VERBOSITY_DEBUG);
                    $indexModel->addFile($file);
                } elseif ($output->isVeryVerbose()) {
                    $output->writeln('Skipping ' . $file . ' due to white/blacklist');
                }
            }
        }
        $output->writeln('Added ' . $indexModel->count() . ' files to index');
        $output->writeln('');

        $indexModel->save($input->getOption('absolute'));
        $output->writeln('Index file successfully created');
        $output->writeln('Path: ' . realpath($indexModel->getIndexFile()), OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln('File: ' . basename($indexModel->getIndexFile()), OutputInterface::VERBOSITY_VERBOSE);

        return 0;
    }
}
