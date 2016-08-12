<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('app:task-list:list')
            ->setDescription('List task lists')
            ->setHelp("This command lists user task lists")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getTasksGoogleService();
        $this->authenticateGoogleClient($input, $output);

        $results = $service->tasklists->listTasklists();
        /** @var \Google_Service_Tasks_TaskList[] $taskLists */
        $taskLists = $results->getItems();
        if (count($taskLists)) {
            $table = new Table($output);
            $table->setHeaders([
                'ID',
                'Title',
                'Updated at',
            ]);
            foreach ($taskLists as $taskList) {
                $updatedAt = new \DateTime($taskList->getUpdated());
                $table->addRow([
                    $taskList->getId(),
                    $taskList->getTitle(),
                    $updatedAt->format('M d, Y \a\t H:i:s'),
                ]);
            }
            $table->render();
        } else {
            $output->writeln('No task lists found.');
        }
    }
}
