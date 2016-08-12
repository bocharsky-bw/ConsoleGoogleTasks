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
            ->setName('app:task:list')
            ->setDescription('List tasks')
            ->setHelp("This command lists user tasks")
            ->addArgument('task-list', InputArgument::REQUIRED, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $taskList = $input->getArgument('task-list');
        $service = $this->getTasksGoogleService();
        $this->authenticateGoogleClient($input, $output);

        $results = $service->tasks->listTasks($taskList);
        /** @var \Google_Service_Tasks_Task[] $tasks */
        $tasks = $results->getItems();
        if (count($tasks)) {
            $table = new Table($output);
            $table->setHeaders([
                '#',
                'ID',
                'Title',
                'Status',
                'Updated at',
            ]);
            foreach ($tasks as $index => $task) {
                $updatedAt = new \DateTime($task->getUpdated());
                $table->addRow([
                    $index + 1,
                    $task->getId(),
                    $task->getTitle(),
                    $task->getStatus(),
                    $updatedAt->format('M d, Y \a\t H:i:s'),
                ]);
            }
            $table->render();
        } else {
            $output->writeln('No tasks found.');
        }
    }
}
