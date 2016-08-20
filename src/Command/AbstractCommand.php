<?php

namespace AlVi\Command;

use AlVi\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends Command
{
    /**
     * @return Application
     */
    public function getApplication()
    {
        $app = parent::getApplication();
        if (!$app instanceof Application) {
            throw new \RuntimeException(sprintf(
                'The "getApplication()" method should return an instance of "%s" class',
                Application::class
            ));
        }

        return $app;
    }

    public function authenticateGoogleClient(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getGoogleClient();

        $credentialsPath = $this->expandHomeDirectory('~/.ConsoleGoogleTasks/credentials.json');
        if (file_exists($credentialsPath)) {
            // Get stored locally access token
            $accessToken = $this->readAccessToken($credentialsPath);
        } else {
            // Request authorization from the user
            $authCode = $this->requestUserAuthentication($client, $input, $output);
            // Exchange authorization code for an access token
            $accessToken = $client->authenticate($authCode);
            // Store access token locally
            $this->writeAccessToken($credentialsPath, $accessToken);
        }
        $client->setAccessToken($accessToken);
        // Refresh the token if it's expired.
        $this->refreshAccessTokenIfExpired($credentialsPath);
    }

    /**
     * @TODO Rename this method to... maybe... "choose"
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function resolveTaskListId(InputInterface $input, OutputInterface $output)
    {
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

    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * @return \Google_Client
     */
    protected function getGoogleClient()
    {
        return $this->getContainer()->offsetGet('google_client');
    }

    /**
     * @return \Google_Service_Tasks
     */
    protected function getTasksGoogleService()
    {
        return $this->getContainer()->offsetGet('google_service_tasks');
    }

    private function requestUserAuthentication(\Google_Client $client, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Open the following link in your browser:');
        $output->writeln($client->createAuthUrl());
        $question = new Question('Enter verification code: ');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        do {
            $answer = $helper->ask($input, $output, $question);
            $authCode = trim($answer);
        } while (empty($authCode));

        return $authCode;
    }

    private function readAccessToken($credentialsPath)
    {
        return json_decode(file_get_contents($credentialsPath), true);
    }

    private function writeAccessToken($credentialsPath, $accessToken)
    {
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
    }

    private function refreshAccessTokenIfExpired($credentialsPath)
    {
        $client = $this->getGoogleClient();

        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
    }

    private function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
}
