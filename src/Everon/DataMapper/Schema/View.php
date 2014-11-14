<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Dependency;
use Everon\DataMapper\Interfaces;
use Everon\DataMapper\Exception;
use Everon\Helper;

class View extends Table implements Interfaces\Schema\View
{
    protected $original_name = null;
    
    /**
     * @param $original_name
     * @param $name
     * @param array $schema
     * @param array $columns
     * @param array $primary_keys
     * @param array $unique_keys
     * @param array $foreign_keys
     */
    public function __construct($original_name, $name, $schema, array $columns, array $primary_keys,  array $unique_keys, array $foreign_keys)
    {
        $this->original_name = $original_name;
        parent::__construct($name, $schema, $columns, $primary_keys, $unique_keys, $foreign_keys);
    }
    
    /**
     * @param string $original_name
     */
    public function setOriginalName($original_name)
    {
        $this->original_name = $original_name;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    public function __sleep()
    {
        return [
            'original_name',
            'pk',
            'name',
            'schema',
            'columns',
            'primary_keys',
            'unique_keys',
            'foreign_keys'
        ];
    }
}
