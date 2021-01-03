# Contributing to ToDo & CO

## Welcome !

Hello! Thank you for taking the time to contribute and help us improve this application! 

## Before getting started

If you would like to contribute, a good place to start is by searching through the issues and pull
requests to see if someone else has a similar idea or question.

Here are a few rules to follow before maintainers can accept and merge your work :

- Follow the Symfony best practices [Symfony-BP](https://symfony.com/doc/current/best_practices.html).

- You MUST follow the PSR-1, PSR-2, PSR-4 and PSR-12. If you do not know what these are please have a look at
 [PSR](https://www.php-fig.org/psr/).
If you need help with these coding style conventions, you can use [PHP_CS](https://github.com/squizlabs/PHP_CodeSniffer).

- You MUST run the test suite (run composer update, and then execute vendor/bin/simple-phpunit).

- You MUST write (or update) unit and functional tests. The code coverage must be at least 70%.

- You SHOULD write documentation with [PHPDoc](https://docs.phpdoc.org/3.0/).

## How to contribute

Here are some examples of how you can contribute:

 - Report a bug
 - Propose a new feature
 - Send a pull request

## How do I submit a bug report?

- Use a clear and descriptive title for the issue to identify the problem.
- Describe the exact steps which reproduce the problem and in as many details as possible.
- Provide specific examples demonstrating these steps.
- Describe the behavior you observed after following these steps and point out the specific problem with the
 behavior.
- Explain which behavior you were expecting to see instead and why.

## Proposing a new feature

 - Use a clear and descriptive title for the issue to identify the suggestion.
 - Explain why this enhancement would be useful to most ToDo & Co users.

## Send a pull request

Before sending a pull request, it is in your interest to open an issue first. By opening an issue you will prevent
long and unnecessary work.
Your future suggestion must be in line with the needs of the project. 
By doing so, the team and the other contributors can discuss and guide you before your start coding.


**Step 1: Fork the project repository**

Find the project's repository on GitHub, and then "fork" it by clicking the Fork button in the upper right corner. 

This action creates a copy of the project repository in your GitHub account.

**Step 2: Clone your fork**

    git clone https://github.com/<your-username>/Projet_8_To_do_list.git
    cd NAME_OF_REPOSITORY

When you have cloned your fork, it should have automatically set your fork as the "origin" remote. To be sure
use this command line:

    git remote -v

This command shows your current remotes. You should see the URL of your fork (which you copied in step 1)
next to the word "origin".

If you do not see an "origin" remote, you can add it by using:

    git remote add origin URL_OF_FORK

**Step 3 : Add the project repository as the "upstream" remote**

    git remote add upstream https://github.com/vincentsig/Projet_8_To_do_list.git

It's a good practice to first synchronize your local repository with the project repository. The command below will
"pull" any changes from the "master" branch of the "upstream" into your local repository. After that, you are good to
start making any changes to your local files.

    git pull upstream master && git push origin master.

**Step 4 : Install Project**

To properly install the application, follow the instructions of the
[README.MD](https://github.com/vincentsig/Projet_8_To_do_list/blob/main/README.md).

**Step 5 : Create a new branch**

Rather than making changes to the project's "master" branch, it's a good practice to create your own branch instead.
This creates an environment for your work that is isolated from the master branch.

Use git checkout -b PREFIX/BRANCH_NAME to create a new branch and then immediately switch to it.

Depending of your contribution you may have to follow one of these PREFIX conventions:

hotfix/ : for fixes  
feature/ : for new features

example: 

    git checkout -b feature/task-add-updatedAt

**Step 6 : Commit your changes**

A good practice to improve the readability of the differents commits is to add a prefix before the description.
Follow the convention below :

* **docs**: Changing documentation
* **feat**: New feature
* **fix**: Hotfix 
* **perf**: Changing code that optimizes performance
* **refactor**: Changing code to refactoring
* **style**: Fixing coding style 
* **test**: Adding or Updating test

After you are done making a set of changes, commit them : 

    git commit -m "prefix/description_of_the_changes"
    git push origin BRANCH_NAME

**Step 7 : Create the pull request**

When opening a "pull request", you are making a "request" that the project repository "pull" changes from your fork.
You will see that the project repository is listed as the "base repository", and your fork is listed as the "head
repository".

Now you can write a descriptive title for your pull request, and then include more details in the body of the pull
request.

## To go further

 - [A successful Git branching model](https://nvie.com/posts/a-successful-git-branching-model/?fbclid=IwAR1M08Y3hBWbPHNc-n-uzQXZ8WE3COg0G8k9wlVafj0dvhSVsMg2ij0Wo8g)
 - [Understanding the GitHub flow](https://guides.github.com/introduction/flow/?fbclid=IwAR242WTjme9nyn-GL5C0qscCtCc8PnOq377lKjZJ3p5M4ant5bcUreC4Jv0)
 - [GitHub actions](https://www.youtube.com/watch?v=cP0I9w2coGU&feature=emb_title)
 - [Clean Code PHP](https://github.com/jupeter/clean-code-php)
