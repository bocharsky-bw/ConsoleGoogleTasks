<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:delete')
            ->setDescription('Delete task')
            ->setHelp("This command deletes user task")
            ->addArgument('task', InputArgument::OPTIONAL, 'Task ID')
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);
        $task = $this->resolveTask($taskList, $input, $output);

        $service = $this->getTasksGoogleService();
        $taskRecourseService = new \Google_Service_Tasks_Resource_Tasks($service, 'tasks', 'delete', [
            "methods" => [
                "delete" => [
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
                    "httpMethod" => "DELETE",
                ],
            ],
        ]);

        // @TODO Add confirmation before actual deleting
        $taskRecourseService->delete($taskList->getId(), $task->getId());

        $output->writeln(sprintf('Task "%s" is deleted (%s)', $task->getTitle(), $task->getId()));
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
}
