<?php

namespace Xspf\Commands\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Commands\CreateCommand;
use Xspf\Index\IndexModel;
use Xspf\Index\IndexModelFactory;
use Xspf\Order\AbstractOrderType;

class OrderIndexCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('index:order')
            ->setDescription('Orders an index file')
            ->addArgument('order-type', InputArgument::OPTIONAL, 'The order type')
            ->addArgument('index-file', InputArgument::OPTIONAL, 'The index file', 'index.' . IndexModel::EXT_COMPRESSED)
            ->addOption('distinct', 't', InputOption::VALUE_NONE, 'Remove duplicates from index file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexModel = IndexModelFactory::factory($input->getArgument('index-file'));
        $indexModel->load();

        if ($input->getOption('distinct')) {
            $skipCount = 0;
            $history = new \ArrayObject();
            $files = $indexModel->getFiles();
            $indexModel->clear();
            foreach ($files as $file) {
                if (isset($history[$file])) {
                    $skipCount++;
                    continue;
                }

                $history[$file] = 1;
                $indexModel->addFile($file);
            }

            if ($skipCount > 0) {
                $output->writeln($skipCount . ' files were skipped due to distinct-option');
            }
        }

        $orderType = AbstractOrderType::factory($input->getArgument('order-type'));
        $orderType->orderIndex($indexModel);
        $indexModel->save();
    }
}
