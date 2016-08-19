<?php

namespace AlVi\Command\Task;

use AlVi\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('task:list')
            ->setDescription('List tasks')
            ->setHelp("This command lists user tasks")
            // @TODO Replace with title instead of ID
            ->addArgument('task-list', InputArgument::OPTIONAL, 'Task list ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticateGoogleClient($input, $output);
        $service = $this->getTasksGoogleService();
        $taskList = $this->resolveTaskListId($input, $output);

        $result = $service->tasks->listTasks($taskList);
        /** @var \Google_Service_Tasks_Task[] $tasks */
        $tasks = $result->getItems();
        if (count($tasks)) {
            $table = new Table($output);
            $table->setHeaders([
                '#',
                'Title',
                'Status',
                'Updated at',
                'ID',
            ]);
            foreach ($tasks as $index => $task) {
                $isTaskCompleted = 'completed' === $task->getStatus();
                $updatedAt = new \DateTime($task->getUpdated());
                $isUpdatedThisYear = date('Y') === $updatedAt->format('Y');
                $table->addRow([
                    $index + 1,
                    $task->getTitle(),
                    $isTaskCompleted ? '[x]' : '[ ]',
                    $updatedAt->format($isUpdatedThisYear ? 'D, M d, H:i' : 'D, M d Y, H:i'),
                    $task->getId(),
                ]);
            }
            $table->render();
        } else {
            $output->writeln('No tasks found.');
        }
    }

    private function resolveTaskListId(InputInterface $input, OutputInterface $output)
    {
        if ($taskListId = $input->getArgument('task-list')) {
            return $taskListId;
        }

        $service = $this->getTasksGoogleService();
        $result = $service->tasklists->listTasklists();
        $taskLists = [];
        $taskListTitles = array_map(function (\Google_Service_Tasks_TaskList $taskList) use (&$taskLists) {
            $key = sprintf('%s (%s)', $taskList->getTitle(), $taskList->getId());
            $taskLists[$key] = $taskList;

            return $key;
        }, $result->getItems());

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Choose task list:',
            $taskListTitles,
            0
        );
        $chosenKey = $helper->ask($input, $output, $question);
        /** @var \Google_Service_Tasks_TaskList $taskList */
        $taskList = $taskLists[$chosenKey];

        return $taskList->getId();
    }
}
