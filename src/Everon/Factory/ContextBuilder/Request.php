<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Factory\ContextBuilder;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;

class Request implements Interfaces\FactoryContextBuilder
{
    use Dependency\Injection\Factory;
    
    use Helper\IsCli;

    
    public function build()
    {
        $args = func_get_args();
        list($_SERVER, $_GET, $_POST, $_FILES) = $args;

        $Request = $this->getFactory()->buildHttpRequest($_SERVER, $_GET, $_POST, $_FILES);
        if ($Request->isAjax()) {
            $Request = $this->getFactory()->buildAjaxRequest($_SERVER, $_GET, $_POST, $_FILES);
        }
        
        if ($this->isCli()) {
            $Request = $this->getFactory()->buildConsoleRequest($_SERVER, $_GET, $_POST, $_FILES);
        }
        
        return $Request;
    }
}