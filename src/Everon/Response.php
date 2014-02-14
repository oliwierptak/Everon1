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

class Response implements Interfaces\Response
{
    protected $data = null;
    protected $result = false;
    protected $guid = null;

    
    public function __construct($guid)
    {
        $this->guid = $guid;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = (bool) $result;
    }

    public function toJson($root='data')
    {
        return json_encode([$root => $this->data]);
    }
    
    public function toText()
    {
        return (string) $this->data;
    }
}