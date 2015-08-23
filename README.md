phpunit-smartrunner
===================

* When `smartrunner` is invoked with an argument `CalcTest.php`, it runs `CalcTest.php` test case.
* When `smartrunner` is invoked with an argument `Calc.php`, it also runs `CalcTest.php`. And it may runs some additional test case related to `Calc.php`.

`smartrunner` resolves dependencies between SUT and test case using xdebug profilier information.

# Usage

```
$ vendor/bin/smartrunner init
```

then, run smartrunner with your favorite IDE, edotir or filesystem watcher.

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


# Integration

# Requirement

- xdebug

# Limitation
