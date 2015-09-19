<?php
namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\FileSystem;

class InitCommand
{
    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    private function defaultConfig()
    {
        return [
            'phpunit' => implode(DIRECTORY_SEPARATOR, ['vendor', 'bin' , 'phpunit']),
            'cacheignores' => [
                'vendor/**/*'
            ]
        ];
    }

    public function run()
    {
        $this->fs->savePhpUnitConfig();
        $this->fs->saveConfigFile($this->defaultConfig());
    }
}
