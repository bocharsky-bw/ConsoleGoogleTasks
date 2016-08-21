<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:clear')
            ->setDescription('Clear tasks')
            ->setHelp("This command clears user tasks")
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);

        $service = $this->getTasksGoogleService();
        $service->tasks->clear($taskList->getId());

        $output->writeln('All completed tasks are cleared from the specified task list');
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }
}
