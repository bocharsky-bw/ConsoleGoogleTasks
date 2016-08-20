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
            ->setName('list:list')
            ->setDescription('List task lists')
            ->setHelp("This command lists user task lists")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $service = $this->getTasksGoogleService();

        $result = $service->tasklists->listTasklists();
        /** @var \Google_Service_Tasks_TaskList[] $taskLists */
        $taskLists = $result->getItems();
        if (count($taskLists)) {
            $table = new Table($output);
            $table->setHeaders([
                '#',
                'Title',
                'Updated at',
                'ID',
            ]);
            foreach ($taskLists as $index => $taskList) {
                $updatedAt = new \DateTime($taskList->getUpdated());
                $table->addRow([
                    $index + 1,
                    $taskList->getTitle(),
                    $updatedAt->format('M d, Y \a\t H:i:s'),
                    $taskList->getId(),
                ]);
            }
            $table->render();
        } else {
            $output->writeln('No task lists found');
        }
    }
}
