<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Console;

use Everon\Interfaces;
use Everon\Exception;

abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    protected $lines = [];
    

    protected function prepareResponse($action)
    {
        $this->getResponse()->setData(
            implode("\n", $this->lines)
        );
    }
    
    protected function response()
    {
        $this->writeln($this->getResponse()->toText());
    }

    protected function writeln($line)
    {
        echo $line."\n";
    }
    
}