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
            ->addArgument('id', InputArgument::OPTIONAL, 'Task list ID')
            ->addArgument('title', InputArgument::OPTIONAL, 'New task list title')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $previousTitle = $taskList->getTitle();
        $newTitle = $this->resolveNewTaskListTitle($input, $output);
        $taskList->setTitle($newTitle);

        $service = $this->getTasksGoogleService();
        $recourseService = new \Google_Service_Tasks_Resource_Tasklists($service, 'tasks', 'patch', [
            "methods" => [
                "patch" => [
                    "parameters" => [
                        'tasklist' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                    ],
                    "path" => "users/@me/lists/{tasklist}",
                    "httpMethod" => "PATCH",
                ],
            ],
        ]);
        $taskList = $recourseService->patch($taskList->getId(), $taskList);

        $output->writeln(sprintf('Task list "%s" is renamed to "%s"', $previousTitle, $taskList->getTitle(), $taskList->getId()));
    }

    private function resolveTaskList(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('id')) {
            // @TODO Query TaskList object
            throw new \Exception('Pending...');
            return $id;
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
