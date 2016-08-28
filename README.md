# ConsoleGoogleTasks

An application for real console geeks which allows to interact with [Google Tasks][google_tasks]
API  directly in your terminal and based on the awesome [Symfony Console][console] component.

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
  * [Create a Symlink](#create-a-symlink)
  * [Short Command Aliases](#short-command-aliases)
* [Command List](#command-list)
  * [Task List Commands](#task-list-commands)
  * [Task Commands](#task-commands)
* [Contribution](#contribution)

## Requirements

To use this application on your local host machine ensure you correspond
to the next requirements:

* PHP `5.5` or higher
* Internet connection to get access to the Google API

## Installation

### With Composer

The preferred way to install this app is to use [Composer][composer]. The next
command installs this project [globally][composer_global] on your local machine:

```bash
$ composer global install bocharsky-bw/console-google-tasks
```

#### Update

[Composer][composer] helps you to easily update it to the latest version:

```bash
$ composer global update bocharsky-bw/console-google-tasks
```

### With Git

An alternative way to install this app is to clone it locally first and then
manually install dependencies with [Composer][composer]:

```bash
$ git clone https://github.com/bocharsky-bw/ConsoleGoogleTasks.git
$ cd ./ConsoleGoogleTasks/
$ composer install
```

#### Update

You still can manually update it with Git by pulling latest changes from the `master`
branch of source repository:

```bash
$ git pull origin master
```

Also don't forget to update dependencies with [Composer][composer] by calling
the next command in the project folder:

```bash
$ composer update
```

## Usage

Start using this console application by calling the next command in your terminal:

```bash
$ ./bin/console.php # Shows list of available commands
```

### Create a Symlink

For the convenience, you can create `console.php` symlink to get a quick access
to the console application globally in any directory you are at:

```bash
$ ln -s /path/to/ConsoleGoogleTasks/bin/console.php /usr/local/bin/todo
```

Then you can simply get access to the console application globally with:

```bash
$ todo # Shows list of available commands
```

> Use whatever alias you want here for the symlink name instead of `todo` one
which uses in example.

### Short Command Aliases

Thanks to the Symfony Console, it allows you do not type a full command.
Just type a short *unique* part of a command starts from the beginning.
For example, to get list of task lists:

```bash
$ todo list:list # Full command name
# Or just use a shortened command alias 
$ todo l:l # This command automatically recognizes by Symfony Console
```

## Command List

List of available commands.

### Task List Commands

| Command     | Description      |
| ----------- | ---------------- |
| list:create | Create task list |
| list:delete | Delete task list |
| list:list   | List task lists  |
| list:rename | Rename task list |
| list:show   | Show task list   |

### Task Commands

| Command         | Description     |
| --------------- | --------------- |
| task:clear      | Clear tasks     |
| task:complete   | Complete task   |
| task:create     | Create task     |
| task:delete     | Delete task     |
| task:incomplete | Incomplete task |
| task:list       | List tasks      |
| task:rename     | Rename task     |
| task:show       | Show task       |

## Contribution

Contribution always are welcome! Feel free to submit an [Issue][issues] or create
a [Pull Request][pulls] if you find a bug or just want to propose an improvement.

In order to propose a new feature, the best way is to submit an [Issue][issues]
and discuss it first.

[Move UP](#consolegoogletasks)


[issues]: https://github.com/bocharsky-bw/ConsoleGoogleTasks/issues
[pulls]: https://github.com/bocharsky-bw/ConsoleGoogleTasks/pulls
[composer]: https://getcomposer.org/
[composer_global]: https://getcomposer.org/doc/03-cli.md#global
[console]: https://symfony.com/doc/current/components/console.html
[google_tasks]: https://mail.google.com/tasks/canvas
