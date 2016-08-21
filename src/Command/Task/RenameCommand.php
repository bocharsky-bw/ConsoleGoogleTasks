<?php

namespace AlVi\Command\Task;

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
            ->setName('task:rename')
            ->setDescription('Rename task')
            ->setHelp("This command renames user task")
            ->addArgument('title', InputArgument::OPTIONAL, 'New task title')
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $task = $this->resolveTask($taskList, $input, $output);
        $previousTitle = $task->getTitle();
        $newTitle = $this->resolveNewTaskTitle($input, $output);
        $task->setTitle($newTitle);

        $service = $this->getTasksGoogleService();
        $taskRecourseService = new \Google_Service_Tasks_Resource_Tasks($service, 'tasks', 'patch', [
            "methods" => [
                "patch" => [
                    "parameters" => [
                        'tasklist' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                        'task' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                    ],
                    "path" => "lists/{tasklist}/tasks/{task}",
                    "httpMethod" => "PATCH",
                ],
            ],
        ]);
        $task = $taskRecourseService->patch($taskList->getId(), $task->getId(), $task);

        $output->writeln(sprintf('Task "%s" is renamed to "%s"', $previousTitle, $task->getTitle(), $task->getId()));
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

    private function resolveNewTaskTitle(InputInterface $input, OutputInterface $output)
    {
        if ($title = $input->getArgument('title')) {
            return $title;
        }

        $question = new Question('Enter new task title: ');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        do {
            $title = $helper->ask($input, $output, $question);
            $title = trim($title);
        } while (empty($title));

        return $title;
    }
}
