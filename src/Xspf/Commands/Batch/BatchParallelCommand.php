<?php

namespace Xspf\Commands\Batch;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Xspf\Commands\BatchCommand;
use Xspf\Utils\Fork;

class BatchParallelCommand extends BatchCommand
{
    protected function configure()
    {
        $this->setName('batch:parallel')
            ->setDescription('Perform multiple xspf operations from multiple batch files')
            ->addArgument('files', InputArgument::IS_ARRAY, '.yml files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fork = new Fork();
        if (!$fork->isForkingSupported()) {
            $output->writeln('<error>Forking is not supported by this host!</error>');

            return 1;
        }

        $files = $input->getArgument('files');
        if (count($files) <= 0) {
            $output->writeln('<error>No files were given!</error>');

            return 1;
        }

        foreach ($files as $file) {
            $fork->fork(function () use ($output, $file) {
                $yaml = Yaml::parse(file_get_contents($file));
                $this->processBatchFile($output, $yaml);
            });
        }
        $status = $fork->wait();
        $output->writeln('Batch finished');

        return $status;
    }
}
