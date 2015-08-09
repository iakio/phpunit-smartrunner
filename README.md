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

```
$ watchy -w  . -- bash -c 'vendor/bin/smartrunner $FILE'
```


# Integration

# Requirement

- xdebug

# Limitation
