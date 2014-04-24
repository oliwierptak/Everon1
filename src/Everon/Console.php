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
        catch (Exception\RouteNotDefined $e) {
            echo "Unknown command: ".$e->getMessage()."\n";
        }
        catch (\Exception $e) {
            echo "Error: ".$e->getMessage()."\n";
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

    /**
     * @inheritdoc
     */
    public function handleExceptions(\Exception $Exception)
    {
        $this->restorePreviousExceptionHandler();
        $this->getLogger()->console($Exception);
        echo $Exception;
    }
    
    public function shutdown()
    {
        $s = parent::shutdown();
        echo "\nExecuted in $s\n";
    }    
}
