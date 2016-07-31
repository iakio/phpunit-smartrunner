<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;


class Config extends \ArrayObject
{
    /**
     * @return Config
     */
    public static function defaultConfig()
    {
        return new self([
            'phpunit' => 'phpunit',
            'cacheignores' => [
                '^vendor'
            ],
        ]);
    }

    /**
     * @param array $other
     * @return Config
     */
    public function merge(array $other)
    {
        return new self(
            array_merge(
                $this->getArrayCopy(),
                $other
            )
        );
    }

    /**
     * @param string $relative_path
     * @return bool
     */
    public function isIgnored($relative_path)
    {
        foreach ($this['cacheignores'] as $pattern) {
            if (preg_match('#'.$pattern.'#', $relative_path)) {
                return true;
            }
        }

        return false;
    }
}
