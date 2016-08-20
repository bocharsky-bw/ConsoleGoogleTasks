<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:show')
            ->setDescription('Show task')
            ->setHelp("This command shows user task")
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $task = $this->resolveTask($taskList, $input, $output);
        $taskUpdatedAt = new \DateTime($task->getUpdated());

        $table = new Table($output);
        $table->addRows([
            ['Title', $task->getTitle()],
            ['Status', $task->getStatus()],
            ['Updated at', $taskUpdatedAt->format('l, F d, Y \a\t H:i:s')],
            ['Completed at', $task->getCompleted()],
            ['Deleted at', $task->getDeleted()],
            ['Hidden at', $task->getHidden()],
            ['ID', $task->getId()],
        ]);
        $table->render();
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            // @TODO Query TaskList object
            throw new \Exception('Pending...');
            return $id;
        }

        return $this->chooseTaskList($input, $output);
    }

    private function resolveTask(\Google_Service_Tasks_TaskList $taskList, InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task')) {
            // @TODO Query Task object
            throw new \Exception('Pending...');
            return $id;
        }

        return $this->chooseTask($taskList, $input, $output);
    }
}
