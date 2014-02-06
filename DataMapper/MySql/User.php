<?php
namespace Everon\DataMapper\MySql;

use Everon\DataMapper\Schema\MySql\Mapper;

class User extends Mapper implements \Everon\DataMapper\Interfaces\User
{
    public function fetchOneByEmail($login)
    {
        $sql = 'SELECT * FROM `%s`.`%s` WHERE login = :%s';
        $sql = sprintf($sql, $this->getSchema()->getDatabase(), $this->getSchemaTable()->getName(), 'login');
        $parameters = ['login' => $login];
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $parameters)->fetch();
    }
}