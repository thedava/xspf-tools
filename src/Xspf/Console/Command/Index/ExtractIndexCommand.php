<?php

namespace Xspf\Console\Command\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Console\Command\AbstractCommand;
use Xspf\Index\IndexModelFactory;

class ExtractIndexCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('index:extract')
            ->setHidden(true)
            ->addArgument('index-file', InputArgument::REQUIRED)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, '', '-');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexModel = IndexModelFactory::factory($input->getArgument('index-file'));
        $indexModel->load();

        $files = [];
        foreach ($indexModel->getFiles() as $file) {
            $files[] = $file;
        }

        $target = $input->getOption('output');
        if ($target === '-') {
            $target = 'php://stdout';
        } else {
            $output->writeln('Extracting ' . count($files) . ' files to ' . $target);
        }

        file_put_contents($target, implode(PHP_EOL, $files));
    }
}
