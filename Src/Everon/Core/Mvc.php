<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core;

use Everon\Interfaces;
use Everon\Exception;

class Mvc extends \Everon\Core implements Interfaces\Core
{
    /**
     * @return void
     */
    protected function response()
    {
        $this->getResponse()->send();
        echo $this->getResponse()->toHtml();
    }
}
