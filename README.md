# Commission calculator application
The application gets file input (executed by CLI) and calculates commission based on rules.
Output is printed out to CLI screen.
All the rules are mapped here: `src/Enum/CommissionRules`.


Number means that the commission is flat and does not depend on complicated rules. 
Classname means that under the rule some complication is hidden.

## Run application in CLI 
Before first run please install all dependencies:
`composer install`

and dump autoload:
`composer dump-autoload`

Then to run it with example input, use:
`composer run go`

## Input/output explanation
1. Example input file is located here `tests/input/input.csv`

2. Example output file is located here `tests/output/output.csv` (only for function tests)

They can be both used in tests or in manual run.

## Code style
PHPCodeStyleFixer is used here to improve quality and standardisation of our code.
You can read about it here:
https://cs.symfony.com/doc/rules/index.html

Symfony standard is used by default. You can change it here: `.php_cs`

To run code style validation, use command: `composer run test-cs`

To run code style fixer, use command: `composer run fix-cs`

## Tests
PHPUnit is installed to simplify testing our code. It's used for unit and Functional tests.

NOTE: Functional test is commented out due to issues with console output reading.

To run php unit, use command `composer run phpunit`


## Other scripts

To run phpunit and then code style validation, use command `composer run test`

### Enjoy! :) 