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

class PdoAdapter implements Interfaces\PdoAdapter 
{
    use Dependency\Injection\Logger;
    
    /**
     * @var \PDO
     */
    protected $Pdo = null;
    
/*    public function __construct($dsn, $username, $password, $options)
    {
        try 
        {
            $this->Database = new \PDO($dsn, $username, $password, $options);
            $this->Database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e)
        {
            throw new Exception\PdoAdapter($e);
        }
    }
    */

    /**
     * @param \PDO $Pdo
     */
    public function __construct(\PDO $Pdo)
    {
        $this->Pdo = $Pdo;
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
    public function exec($sql, $parameters=[], $fetch_mode=\PDO::FETCH_OBJ)
    {
        try
        {
            $this->getLogger()->sql($sql);
            /**
             * @var \PDOStatement $rows
             */
            $rows = $this->Pdo->prepare($sql);
            $rows->execute($parameters);
            
            return $rows->fetchAll($fetch_mode);
        }
        catch (\PDOException $e)
        {
            throw new Exception\Pdo($e);
        }        
    }
    
}