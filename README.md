
# Safebox Rest API

## Deployment
To deploy the application, first make sure you have Docker installed. If you don't, start by installing [Docker](https://docs.docker.com/install/ "Docker") on your system.

Then clone the git project. To do that copy the *adsmurai.bundle* file into the directory where you want to work, and run the following command:  
`git clone adsmurai.bundle`

Now access to the project path:  
`cd adsmurai`

Now you can biuld and run the Docker containers with the following commands:  
`docker-compose build`
`docker-composer up -d`

Then access to the *adsmurai_php* container:  
`docker exec -it adsmurai_php bash`

And access to the project directory:  
`cd adsmurai`

Now install composer packages:  
`composer install`

Finally, create the database and the database schema:  
`bin/console doctrine:database:create`
`bin/console doctrine:schema:create`

You can now start using the API!

## Running tests
Running the tests drops the database and creates a new one so it's recommended to use the *test* environment when running them.

To do that modify your *.env* file and modify the `APP_ENV` value:  
`APP_ENV=test`

Then you can run the tests:  
`bin/phpunit`
