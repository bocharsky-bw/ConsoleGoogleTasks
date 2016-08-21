<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IncompleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:incomplete')
            ->setDescription('Incomplete task')
            ->setHelp("This command incompletes user task")
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        // @TODO Show only completed tasks in list of answers
        $task = $this->resolveTask($taskList, $input, $output);

        $task->setStatus(Status::NEEDS_ACTION);
        $task->setCompleted(\Google_Model::NULL_VALUE); // Completed date should be null

        $service = $this->getTasksGoogleService();
        $task = $service->tasks->patch($taskList->getId(), $task->getId(), $task);

        $output->writeln(sprintf('Task is incompleted', $task->getTitle(), $task->getId()));
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
