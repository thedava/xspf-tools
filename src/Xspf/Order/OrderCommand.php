<?php

namespace Xspf\Order;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;
use Xspf\File;

class OrderCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('order')
            ->setDescription('Orders the given playlist using the given order type')
            ->addArgument('order-type', InputArgument::OPTIONAL, 'The order type')
            ->addArgument('playlist-file', InputArgument::OPTIONAL, 'The playlist file');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Force help command if no order-type is set
        if (!$input->hasArgument('order-type') || $input->getArgument('order-type') == '') {
            $input->setArgument('order-type', 'help');
        } elseif (!in_array($input->getArgument('order-type'), AbstractOrderType::getOrderTypes())) {
            throw new RuntimeException('<error>Unknown or invalid order type given! Use "help" as order type for more information.</error>');
        } elseif (!$input->hasArgument('playlist-file')) {
            throw new RuntimeException('Playlist file is missing');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderType = $input->getArgument('order-type');

        // Special command help
        if ($orderType == 'help') {
            $output->writeln($this->getSynopsis());
            $output->writeln('');

            $output->writeln('asc:    The file will be ordered by video file names in ascending order');
            $output->writeln('desc:   The file will be ordered by video file names in descending order');
            $output->writeln('random: The file will be ordered in random order');

            return 0;
        }

        $order = AbstractOrderType::factory($orderType);
        $file = new File($input->getArgument('playlist-file'));
        $file->load();
        $order->order($file);
        $file->save();

        return 0;
    }
}
