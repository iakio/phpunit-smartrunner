phpunit-smartrunner
===================

# Usage

Add listener in your phpunit.xml(.dist) file.

```
<phpunit>
    <listeners>
        <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
    </listeners>
</phpunit>
```

then, run smartrunner with your favorite filesystem watcher.

## watchy

```
$ watchy -w . -- bash -c 'vendor/bin/smartrunner $FILE'
or
> watchy -w . -- cmd /C "vendor\bin\smartrunner %FILE%"
```

## grunt

```
module.exports = function (grunt) {
    grunt.initConfig({
        shell: {
            smartrunner: {
                file: "",
                command: function () {
                    return "vendor/bin/smartrunner " +
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
