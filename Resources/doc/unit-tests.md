# Unit Tests

## Setup

### 1. Update Composer
Make sure Composer is installed in the root of the project by using
```curl -sS https://getcomposer.org/installer | php```.

Update the vendors for the project using ```php composer.phar update```

### 2. Install and run PHPUnit
Retrieve ```phpunit.phar``` from https://phpunit.de and save into the root of the project.

Run ```php phpunit.phar``` in the root of the project to start the tests.

## Conventions

1. Any added public method should contain unit tests.
2. If current tests are failing... ?
