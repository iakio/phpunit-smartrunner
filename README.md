phpunit-smartrunner
===================

* When `smartrunner` is invoked with an argument `CalcTest.php`, it runs `CalcTest.php` test case.
* When `smartrunner` is invoked with an argument `Calc.php`, it also runs `CalcTest.php`. And it may runs some additional test case related to `Calc.php`.

`smartrunner` resolves dependencies between SUT and test case using xdebug coverage information.

# Install

```
$ composer require --dev iakio/phpunit-smartrunner:dev-master
```

# Usage

```
$ vendor/bin/smartrunner init
```

This creates `.smartrunner/phpunit.xml.dist` file. If you already have
some test code, and you want to store all dependencies between
SUTs and tests certainly, you can run phpunit with this file.

```
$ vendor/bin/phpunit -c .smartrunner/phpunit.xml.dist tests
```

Then, run smartrunner from your favorite IDE, edotir or filesystem watcher.

```
$ vendor/bin/smartrunner run src/Calc.php
```

# Setting Examples

## watchy

```
$ watchy -w . -- bash -c 'vendor/bin/smartrunner run $FILE'
or
> watchy -w . -- cmd /C "vendor\bin\smartrunner run %FILE%"
```

## grunt

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

If you want to use `phpunit.phar`, change your `.smartrunner/config.json`:

```
{
    "phpunit": "php phpunit.phar",
    "cacheignores": [
        "vendor\/**\/*"
    ]
}
```

# Requirement

- xdebug
