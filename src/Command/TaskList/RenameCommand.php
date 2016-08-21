<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class RenameCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('list:rename')
            ->setDescription('Rename task list')
            ->setHelp("This command renames user task list")
            ->addArgument('title', InputArgument::OPTIONAL, 'New task list title')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $newTitle = $this->resolveNewTaskListTitle($input, $output);

        $previousTitle = $taskList->getTitle();
        $taskList->setTitle($newTitle);

        $service = $this->getTasksGoogleService();
        $taskList = $service->tasklists->patch($taskList->getId(), $taskList);

        $output->writeln(sprintf('Task list "%s" is renamed to "%s"', $previousTitle, $taskList->getTitle(), $taskList->getId()));
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('task-list')) {
            return $this->getTasksGoogleService()->tasklists->get($id);
        }

        return $this->chooseTaskList($input, $output);
    }

    private function resolveNewTaskListTitle(InputInterface $input, OutputInterface $output)
    {
        if ($title = $input->getArgument('title')) {
            return $title;
        }

        $question = new Question('Enter new task list title: ');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        do {
            $title = $helper->ask($input, $output, $question);
            $title = trim($title);
        } while (empty($title));

        return $title;
    }
}
