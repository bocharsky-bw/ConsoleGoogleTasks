<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('list:create')
            ->setDescription('Create task list')
            ->setHelp("This command creates user task list")
            ->addArgument('title', InputArgument::OPTIONAL, 'Task list title')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $title = $this->resolveTaskListTitle($input, $output);

        $taskList = new \Google_Service_Tasks_TaskList();
        $taskList->setTitle($title);

        $service = $this->getTasksGoogleService();
        $taskList = $service->tasklists->insert($taskList);

        $output->writeln(sprintf('Task list "%s" is created (%s)', $taskList->getTitle(), $taskList->getId()));
    }

    private function resolveTaskListTitle(InputInterface $input, OutputInterface $output)
    {
        if ($title = $input->getArgument('title')) {
            return $title;
        }

        $question = new Question('Enter task list title: ');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        do {
            $title = $helper->ask($input, $output, $question);
            $title = trim($title);
        } while (empty($title));

        return $title;
    }
}
