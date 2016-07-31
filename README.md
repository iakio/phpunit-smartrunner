phpunit-smartrunner
===================
[![Build Status](https://travis-ci.org/iakio/phpunit-smartrunner.svg?branch=master)](https://travis-ci.org/iakio/phpunit-smartrunner)

`smartrunner` resolves dependencies between SUTs and test cases by code coverage data using phpdbg or xdebug.

* When `smartrunner` is invoked with an argument `CalcTest.php`, it runs `CalcTest.php` test case.
* When `smartrunner` is invoked with an argument `Calc.php`, it also runs `CalcTest.php`. Additionally, it may run all test cases related to `Calc.php`.

# Install

```
$ composer require --dev iakio/phpunit-smartrunner:dev-master
```

# Usage

```
$ vendor/bin/smartrunner init
```

This creates `.smartrunner/phpunit.xml.dist` file. If you already have
some test codes, and you want to store all dependencies between
SUTs and tests certainly, you can run phpunit with this file.

```
$ vendor/bin/phpunit -c .smartrunner/phpunit.xml.dist tests
```

Then, run smartrunner from your favorite IDE, editor or filesystem watcher.

```
$ vendor/bin/smartrunner run src/Calc.php
```

# Setting Examples

## [gomon](https://github.com/c9s/gomon)

```
> gomon -m=\.php$ -R -F -- vendor\bin\smartrunner run
```

## [watchy](https://www.npmjs.com/package/watchy)

```
$ watchy -w . -- bash -c 'vendor/bin/smartrunner run $FILE'
or
> watchy -w . -- cmd /C "vendor\bin\smartrunner run %FILE%"
```

## [grunt](http://gruntjs.com/)

```
module.exports = function (grunt) {
    grunt.initConfig({
        shell: {
            smartrunner: {
                file: "",
                command: function () {
                    return "vendor/bin/smartrunner run " +
                        grunt.config.get('shell.smartrunner.file');
                }
            }

        },
        esteWatch: {
            options: {
                dirs: [
                    'src/**/',
                    'tests/**/',
                ]
            },
            php: function (filePath) {
                grunt.config("shell.smartrunner.file", filePath);
                return ['shell:smartrunner'];
            },
        },
    });
    grunt.loadNpmTasks('grunt-este-watch');
    grunt.loadNpmTasks('grunt-shell');
};
```
# Configuration

If you want to use `phpunit.phar`, change your `.smartrunner/config.php`:

```
{
    "phpunit": "php phpunit.phar",
    "cacheignores": [
        "vendor\/**\/*"
    ]
}
```

# Requirement

- php >= 5.4
- phpdbg or xdebug

# License

This software is released under the MIT License.
