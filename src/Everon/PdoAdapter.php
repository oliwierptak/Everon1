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
    
    protected function beginTransaction($sql, $parameters, $fetch_mode)
    {
        $this->getLogger()->sql($sql."|".print_r($parameters, true));
        /**
         * @var \PDOStatement $statement
         */
        switch ($this->getConnectionConfig()->getDriver()) { //todo: xxx
            case 'mysql':
                $this->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, FALSE);
                break;
        }
        $statement = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $this->getPdo()->beginTransaction();

        if ($parameters !== null) {
            $result = $statement->execute($parameters);
        }
        else {
            $result = $statement->execute();
        }

        $statement->setFetchMode($fetch_mode);
        return $statement;
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
            $statement = $this->beginTransaction($sql, $parameters, $fetch_mode);
            $this->getPdo()->commit();
            return $statement;
        }
        catch (\PDOException $e) {
            if ($this->getPdo()->inTransaction()) {
                $this->getPdo()->rollBack();
            }
            $this->getLogger()->sql_error($sql."|".print_r($parameters, true)); //todo: addFromArray logSql to logger to handle thot
            throw new Exception\Pdo($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function insert($sql, array $parameters=[], $sequence_name=null, $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $statement = $this->beginTransaction($sql, $parameters, $fetch_mode);
            $last_id = $this->getPdo()->lastInsertId($sequence_name);
            $this->getPdo()->commit();
            return $last_id;
        }   
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            throw new Exception\Pdo($e);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function update($sql, array $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        try {
            $statement = $this->beginTransaction($sql, $parameters, $fetch_mode);
            $this->getPdo()->commit();
            return $statement->rowCount();
        }
        catch (\PDOException $e) {
            $this->getPdo()->rollBack();
            throw new Exception\Pdo($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($sql, array $parameters=[], $fetch_mode=\PDO::FETCH_ASSOC)
    {
        return $this->update($sql, $parameters, $fetch_mode);
    }
}