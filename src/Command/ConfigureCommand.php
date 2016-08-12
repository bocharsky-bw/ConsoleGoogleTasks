<?php

namespace AlVi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigureCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:configure')
            ->setDescription('Configure application credentials')
            ->setHelp("This command helps to configure application with user Google credentials")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
