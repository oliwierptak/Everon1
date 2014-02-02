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
     * @inheritdoc
     */
    public function execute($sql, array $parameters=null, $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $this->getLogger()->sql($sql."|".print_r($parameters, true));
            /**
             * @var \PDOStatement $statement
             */
            $this->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);
            $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $this->getPdo()->beginTransaction();
            
            if ($parameters !== null) {
                $result = $statement->execute($parameters);
            }
            else {
                $result = $statement->execute();
            }

            $statement->setFetchMode($fetch_mode);
            
            $this->getPdo()->commit();
            return $statement;
        }
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            $this->getLogger()->sql_error($sql."|".print_r($parameters, true));
            throw new Exception\Pdo($e);
        }
    }    
    
    public function insert($sql, array $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $this->getLogger()->sql($sql."|".print_r($parameters, true));
            /**
             * @var \PDOStatement $statement
             */
            $this->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);
            $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            
            $this->getPdo()->beginTransaction();
            $result = $statement->execute($parameters);  
            $statement->setFetchMode($fetch_mode);
            
            $last_id = $this->getPdo()->lastInsertId();
            $this->getPdo()->commit();
            
            return $last_id;
        }
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            throw new Exception\Pdo($e);
        }
    }
    
    public function update($sql, array $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $this->getLogger()->sql($sql."|".print_r($parameters, true));
            /**
             * @var \PDOStatement $statement
             */
            $this->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);
            $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            
            $this->getPdo()->beginTransaction();
            $result = $statement->execute($parameters);  
            $statement->setFetchMode($fetch_mode);
            $this->getPdo()->commit();
            
            return $statement->rowCount();
        }
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            throw new Exception\Pdo($e);
        }
    }
    
    public function delete($sql, array $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $this->getLogger()->sql($sql."|".print_r($parameters, true));
            /**
             * @var \PDOStatement $statement
             */
            $this->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);
            $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            
            $this->getPdo()->beginTransaction();
            $result = $statement->execute($parameters);  
            $statement->setFetchMode($fetch_mode);
            $this->getPdo()->commit();
            
            return $statement->rowCount();
        }
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            throw new Exception\Pdo($e);
        }
    }
}