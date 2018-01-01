<?php

namespace Xspf\Index;

use DavaHome\Console\Helper\ProgressBarOptions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;

class CreateIndexCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('create-index')
            ->setDescription('Create an index file')
            ->setHelp(implode(PHP_EOL, [
                'This command creates an index file used by various other xspf-tools commands',
                '',
                'Index files can be used to dynamically create playlist files (e.g. if you use ',
                'Windows and Linux the paths of the files may be inconsistent. The index file ',
                'stores all paths relative to provide cross-system-support)',
            ]))
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The path of the file', 'index.xd')
            ->addOption('no-progress', null, InputOption::VALUE_NONE, 'Suppress the progressbar');
    }

    /**
     * @param string      $file
     * @param IndexModel  $indexModel
     * @param ProgressBar $progressBar
     */
    protected function addFile($file, IndexModel $indexModel, ProgressBar $progressBar)
    {
        $indexModel->addFile($file);
        $progressBar->advance();
    }

    /**
     * @param string      $dir
     * @param IndexModel  $indexModel
     * @param ProgressBar $progressBar
     */
    protected function openDir($dir, IndexModel $indexModel, ProgressBar $progressBar)
    {
        foreach (glob($dir . '/*') as $fileOrDir) {
            if (is_dir($fileOrDir)) {
                $this->openDir($fileOrDir, $indexModel, $progressBar);
            } else {
                $this->addFile($fileOrDir, $indexModel, $progressBar);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating index. This may take a while...');


        $progressBarOutput = ($input->getOption('no-progress')) ? new BufferedOutput() : $output;
        $progressBar = $this->createProgressBar($progressBarOutput);
        $progressBar->setFormat(ProgressBarOptions::FORMAT_DEBUG_NOMAX);
        $progressBar->start();

        $indexModel = new IndexModel($input->getOption('output'));
        foreach ($input->getArgument('file-or-folder') as $fileOrFolder) {
            if (is_dir($fileOrFolder)) {
                $this->openDir($fileOrFolder, $indexModel, $progressBar);
            } else {
                $this->addFile($fileOrFolder, $indexModel, $progressBar);
            }
        }
        $progressBar->setProgress($indexModel->count());
        $progressBar->finish();
        $progressBarOutput->writeln('');
        $progressBarOutput->writeln('');

        $indexModel->save();
        $output->writeln('Index file successfully created');
        $output->writeln('Path: ' . realpath($indexModel->getIndexFile()));
    }
}
