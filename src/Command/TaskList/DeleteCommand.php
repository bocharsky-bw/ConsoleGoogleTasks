<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('list:delete')
            ->setDescription('Delete task list')
            ->setHelp("This command deletes user task list")
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);

        // @TODO Add confirmation before actual deleting
        $service = $this->getTasksGoogleService();
        $service->tasklists->delete($taskList->getId());

        $output->writeln(sprintf('Task list "%s" is deleted (%s)', $taskList->getTitle(), $taskList->getId()));
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }
}
