<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('list:delete')
            ->setDescription('Delete task list')
            ->setHelp("This command deletes user task list")
            // @TODO Use task list title instead of ID
            ->addArgument('id', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $taskList = $this->resolveTaskList($input, $output);

        $service = $this->getTasksGoogleService();
        $recourseService = new \Google_Service_Tasks_Resource_Tasklists($service, 'tasks', 'delete', [
            "methods" => [
                "delete" => [
                    "parameters" => [
                        'tasklist' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                    ],
                    "path" => "users/@me/lists/{tasklist}",
                    "httpMethod" => "DELETE",
                ],
            ],
        ]);

        // @TODO Add confirmation before actual deleting
        $recourseService->delete($taskList->getId());

        $output->writeln(sprintf('Task list "%s" is deleted (%s)', $taskList->getTitle(), $taskList->getId()));
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
}
