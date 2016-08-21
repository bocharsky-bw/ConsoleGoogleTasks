<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:list')
            ->setDescription('List tasks')
            ->setHelp("This command lists user tasks")
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);

        $service = $this->getTasksGoogleService();
        $result = $service->tasks->listTasks($taskList->getId());
        /** @var \Google_Service_Tasks_Task[] $tasks */
        $tasks = $result->getItems();
        if (count($tasks)) {
            $table = new Table($output);
            $table->setHeaders([
                '#',
                'Title',
                'Status',
                'Updated at',
                'ID',
            ]);
            foreach ($tasks as $index => $task) {
                $isTaskCompleted = 'completed' === $task->getStatus();
                $updatedAt = new \DateTime($task->getUpdated());
                $isUpdatedThisYear = date('Y') === $updatedAt->format('Y');
                $table->addRow([
                    $index + 1,
                    $task->getTitle(),
                    $isTaskCompleted ? '[x]' : '[ ]',
                    $updatedAt->format($isUpdatedThisYear ? 'D, M d, H:i' : 'D, M d Y, H:i'),
                    $task->getId(),
                ]);
            }
            $table->render();
        } else {
            $output->writeln('No tasks found');
        }
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }
}
