# Git Start Project API

## Setup instructions
- Download the code
- `cd gitstartproductapi`
- Install dependenceis `composer install` 
- Update the `env` file to match your database details
- Run `symfony server:start` to start the project

You can now test the api on an http client like postman. 

## Documentation
Access `baseurl/api/docs.html` for the documentation


## Prepare test
- Create test database `symfony console --env=test doctrine:database:create`
- Migrate test database `symfony console --env=test doctrine:database:create`
- Populate your test db `symfony console --env=test hautelook:fixtures:load`


## Pending
- Completion of docker support
- PHPStan and PHP_CodeSniffer

## Problems
### Login issues in api
I had loads of difficulties authenticating and running my test as i regularly had error `[error] Uncaught PHP Exception Symfony\Component\HttpKernel\Exception\NotFoundHttpException: "Unable to find the controller for path "/api/login_check". The route is wrongly configured." at HttpKernel.php line 165`. 
After solving the error in the api (commented out the main section in packages/security.yaml file), i could not get the test running because of the same error. All trials to remedy the situation prooved unsuccessful. 