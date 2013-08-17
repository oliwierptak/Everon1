<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core\Console;

use Everon\Interfaces;
use Everon\Exception;

abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    /**
     * @return void
     */
    protected function prepareResponse()
    {
        $this->getResponse()->setData(['console'=>'yes']);
    }
    
    /**
     * @return void
     */
    public function response()
    {
        $data = explode("\n", $this->getResponse()->toText());
        foreach ($data as $line) {
            $this->writeln($line);
        }
    }

    protected function writeln($line)
    {
        echo "${line}\n";
    }
    
}