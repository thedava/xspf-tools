<?php

namespace Xspf\Console\Command\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Console\Command\AbstractCommand;
use Xspf\Index\IndexModelFactory;
use Xspf\Order\AbstractOrderType;
use Xspf\WhiteAndBlacklistService;

class FilterIndexCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('index:filter')
            ->setDescription('Filter an index file with whitelist/blacklist definitions')
            ->addArgument('index-file', InputArgument::REQUIRED, 'The source index file')
            ->addArgument('target-file', InputArgument::OPTIONAL, 'The target index file (source will be overwritten if not specified)', null)
            ->addOption('distinct', 't', InputOption::VALUE_NONE, 'Avoid duplicates in playlist file')
            ->addOption('order', '', InputOption::VALUE_REQUIRED, 'Order the index file (asc, desc, random)', null);
        WhiteAndBlacklistService::appendOptionsToCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('index-file');
        $target = $input->getArgument('target-file');
        if (empty($target)) {
            $target = $source;
        }

        $whiteAndBlacklistService = WhiteAndBlacklistService::createFromCommandInput($input);
        $indexModel = IndexModelFactory::factory($source);
        $indexModel->load();

        $order = $input->getOption('order');
        if ($order !== null) {
            $orderType = AbstractOrderType::factory($order);
            $orderType->orderIndex($indexModel);
        }

        $skipCount = 0;
        $history = [];
        $distinct = $input->getOption('distinct');

        $this->trackPerformance('Checking files (white-/blacklist)');
        $targetIndex = IndexModelFactory::factory($target, false);
        foreach ($indexModel->getFiles() as $file) {
            if ($distinct) {
                if (isset($history[$file])) {
                    $skipCount++;
                    continue;
                }

                $history[$file] = 1;
            }

            if ($whiteAndBlacklistService->isFileAllowed($file)) {
                $targetIndex->addFile($file);
            }
        }
        $this->trackPerformance('White-/Blacklist check finished');

        if ($skipCount > 0) {
            $output->writeln($skipCount . ' files were skipped due to distinct-option');
        }

        $targetIndex->save();
        if ($target === $source) {
            $output->writeln(sprintf('Removed %d file(s) from index', $indexModel->count() - $targetIndex->count()));
        } else {
            $output->writeln(sprintf('Created new index with %d file(s) (initially was %d)', $targetIndex->count(), $indexModel->count()));
        }

        return 0;
    }
}
