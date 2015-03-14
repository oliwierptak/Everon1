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

use Everon\DataMapper\Exception\ConnectionItem as ConnectionItemException;
use Everon\DataMapper\Interfaces;
use Everon\Exception;
use Everon\Helper;

class Item implements Interfaces\ConnectionItem
{
    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsArrayKey;
    use Helper\Immutable;
    use Helper\ToArray;

    protected $driver = null;
    protected $host = null;
    protected $port = null;
    protected $database = null;
    protected $user = null;
    protected $password = null;
    protected $encoding = null;
    protected $options = [];
    protected $adapter_name = null;
    protected $schema = '';
    protected $dsn = null;


    /**
     * @param array $data
     *
     * <code>
     * Content of $data:
     * driver = mysql
     * host = localhost
     * port = 3306
     * database = everon
     * user = root
     * password =
     * default = true
     * encoding = UTF8
     * mapper = MySql
     * </code>
     */
    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->driver = $data['driver'];
        $this->host = $data['host'];
        $this->port = $data['port'];
        $this->database = $data['database'];
        $this->user = $data['user'];
        $this->password = $data['password'];
        $this->encoding = $data['encoding'];
        
        if (isset($data['options'])) {
            $this->options = $this->arrayMergeDefault($this->options, $data['options']);
        }
        
        if (isset($data['adapter_name'])) {
            $this->adapter_name = $data['adapter_name'];
        }
        
        if (isset($data['schema'])) {
            $this->schema = $data['schema'];
        }
        
        $this->data = $data;
        $this->dsn = $this->getDsn();
        
        $this->lock();
    }
    
    protected function validate($data)
    {
        try {
            $properties = [
                'driver', 'host', 'port', 'database', 'user', 'password', 'encoding'
            ];
            
            foreach ($properties as $property_name) {
                $this->assertIsArrayKey($property_name, $data, $property_name);
            }
        }
        catch (Exception\Asserts $e) {
            throw new ConnectionItemException('Missing required parameter: "%s"', $e->getMessage(), $e);
        }
    }
    
    public function getDsn()
    {
        if ($this->dsn === null) {
            switch ($this->getDriver()) { //todo: xxx
                case 'mysql':
                    $this->dsn = sprintf(
                        '%s:dbname=%s;host=%s;port=%s;charset=UTF8', //'mysql:dbname=testdb;host=127.0.0.1',
                        $this->driver,
                        $this->database,
                        $this->host,
                        $this->port
                    );
                    break;
                
                case 'pgsql':
                    $this->dsn = sprintf(
                        '%s:host=%s;port=%d;dbname=%s;user=%s;password=%s;options= --client_encoding=UTF8', //pgsql:host=localhost;port=5432;dbname=testdb;user=postgres;password=easy
                        $this->driver,
                        $this->host,
                        $this->port,
                        $this->database,
                        $this->user,
                        $this->password
                    );
                    break;
            }

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
    
    public function getAdapterName()
    {
        if ($this->adapter_name === null) {
            switch (strtolower($this->getDriver())) { //todo xxx
                case 'mysql':
                    $this->adapter_name = 'MySql';
                    break;
                
                case 'pgsql':
                    $this->adapter_name = 'PostgreSql';
                    break;
            }
        }
        
        if ($this->adapter_name === null) {
            throw new ConnectionItemException('Database driver not set');
        }
        
        return $this->adapter_name;
    }
    
    public function getDatabase()
    {
        return $this->database;
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
        return $this->options;
    }
    
    public function toPdo()
    {
        return [
            $this->getDsn(),
            $this->getUsername(),
            $this->getPassword(),
            $this->getOptions()
        ];        
    }

    public function __sleep()
    {
        return [
            'driver',
            'host',
            'port',
            'database',
            'user',
            'password',
            'encoding',
            'options',
            'adapter_name',
            'schema',
            'dsn',
        ];
    }

    public static function __set_state(array $parameters)
    {
        $Item = new static($parameters['data'], []);
        
        foreach ($parameters as $key => $value) {
            $Item->$key = $value;
        }

        return $Item;
    }
}
