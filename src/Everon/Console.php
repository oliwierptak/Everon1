<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Dependency;
use Everon\Interfaces;

class Console extends Core implements Interfaces\Core
{
    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            parent::run($RequestIdentifier);
        }
        catch (\Exception $e) {
            $this->showException($e, $this->Controller);
        }
        finally {
            $this->getLogger()->console(
                sprintf(
                    '[%d] %s %s',
                    $this->getResponse()->getResult(), $this->getRequest()->getMethod(), $this->getRequest()->getPath()
                )
            );
        }
    }

    public function shutdown()
    {
        $s = parent::shutdown();
        echo "\nExecuted in $s\n";
    }    
}
