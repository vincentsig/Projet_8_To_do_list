# Projet_8_To_do_list

## Introduction

This is my last project of my apprenticeship training with OpenClassrooms. My mission is to improve an existing ToDo & Co application. You can have a look to the original repository [here](https://github.com/saro0h/projet8-TodoList).

## Information

ToDo & Co is a startup whose core business is an application to manage daily tasks. The company has just been set up, and the application had to be developed at full speed to allow potential investors to be shown that the concept is viable (known as Minimum Viable Product or MVP).

For this project I'm in charge of the following tasks:

 - The implementation of new features
 - The correction of some anomalies and the implementation of automated tests
 - Create a code quality & application performance audit
 - Create a technical documentation concerning the implementation of authentication
 - Create a document explaining how to contribute to the project

## Code quality

<a href="https://codeclimate.com/github/vincentsig/Projet_8_To_do_list/maintainability"><img src="https://api.codeclimate.com/v1/badges/f77a295a7aa6597edfa8/maintainability" /></a>

## Development environment 

- PHP  7.4.8
- Symfony 5.1.9
- Apache 2.4.46a
- MySQL 8.0.22

## Installation

**1. Download or clone the github repository:**  

  [To_do_list Repository](https://github.com/vincentsig/Projet_8_To_do_list.git)

**2. Install the back-end  dependencies**

      composer install
      
**3. Install npm for the front-end dependencies:**
    
      npm install    
      
**4. Build assets with webpack Encore**
    
      npm run watch


**5. Setup your environment**

Create an .env.local file and fill in the required environment variables if needed.

      ###> doctrine/doctrine-bundle ###
            DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

**6. Create the Database**

      php bin/console doctrine:database:create

**7. Update schema**
 
      php bin/console doctrine:schema:update --force

**8. Load the dataFixtures**

      php bin/console doctrine:fixtures:load

## Authentification

      login : user_test
      password : 123456

## Contributing

You can contribute to the project : [CONTRIBUTING](https://github.com/vincentsig/Projet_8_To_do_list/blob/main/CONTRIBUTING.md)




      
    
    
