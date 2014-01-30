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

use Everon\Exception;
use Everon\Dependency;
use Everon\DataMapper\Interfaces\ConnectionItem;

class PdoAdapter implements Interfaces\PdoAdapter 
{
    use Dependency\Injection\Logger;
    
    /**
     * @var \PDO
     */
    protected $Pdo = null;


    /**
     * @var ConnectionItem
     */
    protected $ConnectionConfig = null;


    /**
     * @param \PDO $Pdo
     * @param ConnectionItem $Connection
     */
    public function __construct(\PDO $Pdo, ConnectionItem $Connection)
    {
        $this->Pdo = $Pdo;
        $this->ConnectionConfig = $Connection;
    }
    
    /**
     * @inheritdoc
     */
    public function setConnectionConfig(ConnectionItem $ConnectionConfig)
    {
        $this->ConnectionConfig = $ConnectionConfig;
    }

    /**
     * @inheritdoc
     */
    public function getConnectionConfig()
    {
        return $this->ConnectionConfig;
    }
    
    /**
     * @inheritdoc
     */
    public function setPdo(\PDO $Pdo)
    {
        $this->Pdo = $Pdo;
    }

    /**
     * @inheritdoc
     */    
    public function getPdo()
    {
        return $this->Pdo;
    }
    
    /**
     * @param $sql
     * @param array $parameters
     * @param int $fetch_mode
     * @return array
     * @throws Exception\Pdo
     */
    public function execute($sql, $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            foreach ($parameters as $index => $value) {
                $parameters[':'.$index] = $parameters[$index];
                unset($parameters[$index]);
            }
        
            $this->getLogger()->sql($sql."|".print_r($parameters, true));
            /**
             * @var \PDOStatement $statement
             */
            $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $statement->execute($parameters);

            return $statement->fetchAll();
        }
        catch (\PDOException $e) {
            throw new Exception\Pdo($e);
        }        
    }
}