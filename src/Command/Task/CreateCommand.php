<?php

namespace AlVi\Command\Task;

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
            ->setName('task:create')
            ->setDescription('Create task')
            ->setHelp("This command creates user task")
            ->addArgument('title', InputArgument::OPTIONAL, 'Task title')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $title = $this->resolveTaskTitle($input, $output);

        $service = $this->getTasksGoogleService();
        $recourseService = new \Google_Service_Tasks_Resource_Tasks($service, 'tasks', 'insert', [
            "methods" => [
                "insert" => [
                    "parameters" => [
                        'tasklist' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                    ],
                    "path" => "lists/{tasklist}/tasks",
                    "httpMethod" => "POST",
                ],
            ],
        ]);

        $task = new \Google_Service_Tasks_Task();
        $task->setTitle($title);
        $task = $recourseService->insert($taskList->getId(), $task);

        $output->writeln(sprintf('Task "%s" is created (%s)', $task->getTitle(), $task->getId()));
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

    private function resolveTaskTitle(InputInterface $input, OutputInterface $output)
    {
        if ($title = $input->getArgument('title')) {
            return $title;
        }

        $question = new Question('Enter task title: ');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        do {
            $title = $helper->ask($input, $output, $question);
            $title = trim($title);
        } while (empty($title));

        return $title;
    }
}
