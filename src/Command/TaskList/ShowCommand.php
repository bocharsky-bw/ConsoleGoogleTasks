<?php

namespace AlVi\Command\TaskList;

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
            ->setName('list:show')
            ->setDescription('Show task list')
            ->setHelp("This command shows user task list")
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $updatedAt = new \DateTime($taskList->getUpdated());

        $table = new Table($output);
        $table->addRows([
            ['Title', $taskList->getTitle()],
            ['Updated at', $updatedAt->format('l, F d, Y \a\t H:i:s')],
            ['ID', $taskList->getId()],
        ]);
        $table->render();
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }
}
