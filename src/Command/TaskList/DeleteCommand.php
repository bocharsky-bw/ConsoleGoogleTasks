<?php

namespace AlVi\Command\TaskList;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $id = $this->resolveTaskListId($input, $output);

        $service = $this->getTasksGoogleService();
        $recourseService = new \Google_Service_Tasks_Resource_Tasklists($service, 'tasks', 'delete', array(
            "methods" => array(
                "delete" => array(
                    "parameters" => [
                        'tasklist' => [
                            'required' => true,
                            'type' => 'string',
                            'location' => 'path',
                        ],
                    ],
                    "path" => "users/@me/lists/{tasklist}",
                    "httpMethod" => "DELETE",
                )
            )
        ));

        $recourseService->delete($id);

        $output->writeln(sprintf('Task list is deleted (%s)', $id));
    }

    protected function resolveTaskListId(InputInterface $input, OutputInterface $output)
    {
        if ($id = $input->getArgument('id')) {
            return $id;
        }

        return parent::resolveTaskListId($input, $output);
    }
}
