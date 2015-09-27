<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand;

use DOMDocument;
use DOMAttr;

class PhpunitConfigGenerator
{
    public function generate($original)
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($original);

        $listeners_nodes = $doc->getElementsByTagName('listeners');
        if ($listeners_nodes->length === 0) {
            $listeners = $doc->firstChild->appendChild($doc->createElement('listeners'));
        } else {
            $listeners = $listeners_nodes->item(0);
        }
        $listener = $doc->createElement('listener');
        $listener->setAttributeNode(
            new DomAttr('class', 'iakio\phpunit\smartrunner\DependencyListener'));
        $listeners->appendChild($listener);

        return $doc->saveXML();
    }
}
