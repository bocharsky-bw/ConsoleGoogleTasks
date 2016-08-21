<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:delete')
            ->setDescription('Delete task')
            ->setHelp("This command deletes user task")
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $task = $this->resolveTask($taskList, $input, $output);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete task? Y/N: ', false);
        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $service = $this->getTasksGoogleService();
        $service->tasks->delete($taskList->getId(), $task->getId());

        $output->writeln(sprintf('Task "%s" is deleted (%s)', $task->getTitle(), $task->getId()));
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
