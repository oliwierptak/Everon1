<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Task;

use Everon\Dependency;

class Manager implements Interfaces\Manager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\Logger;


    /**
     * @inheritdoc
     */
    public function process(array $tasks)
    {
        /**
         * @var $Task Interfaces\Item
         */
        foreach ($tasks as $Task) {
            $this->processOne($Task);
        }
    }

    /**
     * @inheritdoc
     */
    public function processOne(Interfaces\Item $Task)
    {
        $result = false;
        
        try {
            $Task->markAsProcessing();
            $result = $Task->execute();
        }
        catch (\Exception $e) {
            $Task->setError($e);
            $this->getLogger()->error($e);
        }
        finally {
            if ($result) {
                $Task->markAsExecuted();
            }
            else {
                $Task->markAsFailed();
            }
            
            $Task->setResult($result);
            $Task->setExecutedAt($this->getFactory()->buildDateTime());
        }
    }
}