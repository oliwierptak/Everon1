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

use Everon\Exception;
use Everon\Interfaces;
use Everon\RequestIdentifier;

class Core extends \Everon\Core implements Interfaces\Core
{
    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            parent::run($RequestIdentifier);
        }
        catch (Exception\RouteNotDefined $e) {
            $NotFound = new \Exception('Unknown command: '.$e->getMessage());
            $this->showException($NotFound, $this->Controller);
        }
        catch (\Exception $e) {
            $this->showException($e, $this->Controller);
        }
    }

    public function shutdown()
    {
        $s = parent::shutdown();
        
        $this->getLogger()->log('response',
            sprintf(
                '[%d] %s %s',
                $this->getResponse()->getResult(), $this->getRequest()->getMethod(), $this->getRequest()->getPath()
            )
        );

        echo "\nExecuted in $s\n";
    }    
}
