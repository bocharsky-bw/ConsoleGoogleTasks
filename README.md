# ConsoleGoogleTasks

An application for real console geeks which allows to interact with Google Tasks API 
directly in your terminal and based on the awesome [Symfony Console][console] component.

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [Contribution](#contribution)

## Requirements

To use this application on your local host machine ensure you correspond
to the next requirements:

* PHP `5.5` or higher
* Internet Connection to call Google API

## Installation

The preferred way to install this app is to clone it locally and install dependencies
with [Composer][composer]:

```bash
$ git clone https://github.com/bocharsky-bw/ConsoleGoogleTasks.git
$ cd ./ConsoleGoogleTasks/
$ composer install
```

## Usage

Start using console application with next command:

```bash
bin/console.php
```

## Contribution

Contribution always are welcome! Feel free to submit an [Issue][issues] or create
a [Pull Request][pulls] if you find a bug or just want to propose an improvement.

In order to propose a new feature, the best way is to submit an [Issue][issues]
and discuss it first.

[Move UP](#consolegoogletasks)


[issues]: https://github.com/bocharsky-bw/ConsoleGoogleTasks/issues
[pulls]: https://github.com/bocharsky-bw/ConsoleGoogleTasks/pulls
[composer]: https://getcomposer.org/
[console]: https://symfony.com/doc/current/components/console.html
