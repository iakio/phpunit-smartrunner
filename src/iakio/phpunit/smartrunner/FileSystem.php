<?php
namespace iakio\phpunit\smartrunner;

class FileSystem
{
    /**
     * @var string
     */
    private $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

    public function normalizePath($path)
    {
        return str_replace(realpath($this->root) . DIRECTORY_SEPARATOR, "", realpath($path));
    }
}
