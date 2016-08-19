<?php

namespace AlVi\Command\App;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigureCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('app:configure')
            ->setDescription('Configure application credentials')
            ->setHelp('This command helps to configure application with user Google credentials')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);

        $output->writeln('Application is configured and ready to use!');
    }
}
