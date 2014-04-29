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
use Everon\Interfaces\Response2;

class Response implements Interfaces\Response
{
    protected $data = null;
    protected $result = false;
    protected $guid = null;

    /**
     * @param $guid
     */
    public function __construct($guid)
    {
        $this->guid = $guid;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->result = (bool) $result;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->data);
    }

    /**
     * @return string
     */
    public function toText()
    {
        return (string) $this->data;
    }
}