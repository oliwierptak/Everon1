<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\Helper;

class Session extends Helper\Collection implements Interfaces\Session
{
    /**
     * @var string
     */
    protected $guid = null;

    /**
     * @var \DateTime
     */
    protected $start_time = null;
    
    
    /**
     * @param $evrid
     * @param array $data
     */
    public function __construct($evrid, array $data)
    {
        $this->guid = $evrid;
        $this->start_time = new \DateTime();
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param \DateTime $start_time
     */
    public function setStartTime(\DateTime $start_time)
    {
        $this->start_time = $start_time;
    }
}