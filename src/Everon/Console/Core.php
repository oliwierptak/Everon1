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

    /**
     * @param \Exception $Exception
     * @param \Everon\Interfaces\Controller|null $Controller
     */
    protected function showException(\Exception $Exception, $Controller)
    {
        $this->getLogger()->error($Exception);

        /**
         * @var \Everon\Interfaces\Controller $Controller
         */
        if ($Controller === null) {
            echo 'Error: '. $Exception->getMessage();
        }
        else if ($Controller instanceof Interfaces\Controller) {
            $Controller->showException($Exception);
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
