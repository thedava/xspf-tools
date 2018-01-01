<?php

namespace Xspf\Index;

use DavaHome\Console\Helper\ProgressBarOptions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('index-file', InputArgument::OPTIONAL, 'The path of the file', 'index.xd');
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
                $indexModel->addFile($fileOrDir);
                $progressBar->advance();
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating index. This may take a while...');
        $progressBar = $this->createProgressBar($output);
        $progressBar->setFormat(ProgressBarOptions::FORMAT_DEBUG_NOMAX);
        $progressBar->start();

        $indexModel = new IndexModel($input->getArgument('index-file'));
        $this->openDir(getcwd(), $indexModel, $progressBar);
        $progressBar->setProgress($indexModel->count());
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('');

        $indexModel->save();
        $output->writeln('Index file successfully created');
        $output->writeln('Path: ' . realpath($indexModel->getIndexFile()));
    }
}
