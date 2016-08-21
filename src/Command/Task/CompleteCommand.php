<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:complete')
            ->setDescription('Complete task')
            ->setHelp("This command completes user task")
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        // @TODO Show only uncompleted tasks in list of answers
        $task = $this->resolveTask($taskList, $input, $output);

        $task->setStatus(Status::COMPLETED);

        $service = $this->getTasksGoogleService();
        $task = $service->tasks->patch($taskList->getId(), $task->getId(), $task);

        $output->writeln(sprintf('Task is completed', $task->getTitle(), $task->getId()));
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }

    private function resolveTask(\Google_Service_Tasks_TaskList $taskList, InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task')) {
            return $this->getTasksGoogleService()->tasks->get($taskList->getId(), $id);
        }

        return $this->chooseTask($taskList, $input, $output);
    }
}
