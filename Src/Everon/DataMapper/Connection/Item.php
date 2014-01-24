<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Connection;

use Everon\DataMapper\Interfaces;
use Everon\Helper;

class Item implements Interfaces\ConnectionItem
{
    use Helper\Immutable;
    use Helper\ToArray;

    protected $driver = null;
    protected $host = null;
    protected $port = null;
    protected $name = null;
    protected $user = null;
    protected $password = null;
    protected $encoding = null;
    protected $pdo_options = null;
    protected $mapper = null;
    
    protected $dsn = null;


    /**
     * @param array $data
     *
     * <code>
     * Content of $data:
     * driver = mysql
     * host = localhost
     * port = 3306
     * name = everon
     * user = root
     * password =
     * default = true
     * encoding = UTF8
     * mapper = MySql
     * </code>
     */
    public function __construct(array $data)
    {
        $this->driver = $data['driver'];
        $this->host = $data['host'];
        $this->port = $data['port'];
        $this->name = $data['name'];
        $this->user = $data['username'];
        $this->password = $data['password'];
        $this->encoding = $data['encoding'];
        $this->pdo_options = $data['pdo_options'];
        $this->mapper = $data['mapper'];

        $this->data = $data;
        $this->dsn = $this->getDsn();
        $this->lock();
    }
    
    public function getDsn()
    {
        if ($this->dsn === null) {
            $this->dsn = sprintf(
                '%s:dbname=%s;host=%s;port=%s', //'mysql:dbname=testdb;host=127.0.0.1',
                $this->driver,
                $this->name,
                $this->host,
                $this->port
            );
        }
        
        return $this->dsn;
    }

    public function getDriver()
    {
        return $this->driver;
    }
    
    public function getHost()
    {
        return $this->host;
    }
    
    public function getMapper()
    {
        if ($this->mapper === null) {
            switch (strtolower($this->getDriver())) {
                case 'mysql':
                    $this->mapper = 'MySql';
                    break;
            }
        }
        
        return $this->mapper;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getEncoding()
    {
        return $this->encoding;
    }
    
    public function getUsername()
    {
        return $this->user;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getOptions()
    {
        return $this->pdo_options;
    }
    
    public function toPdo()
    {
        return [
            $this->getDsn(),
            $this->getUsername(),
            $this->getPassword(),
            [\PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES \'%s\'', $this->encoding)]
        ];        
    }
}
