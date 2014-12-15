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

class Session implements Interfaces\Session
{
    use Helper\ToArray;
    
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
     */
    public function __construct($evrid)
    {
        $this->guid = $evrid;
        $this->start_time = new \DateTime();
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

    /**
     * @inheritdoc
     */
    public function setFlashMessage($flash_message)
    {
        $this->set('flash_message', $flash_message);
    }

    /**
     * @inheritdoc
     */
    public function getFlashMessage()
    {
        return $this->get('flash_message');
    }

    /**
     * @inheritdoc
     */
    public function resetFlashMessage()
    {
        $this->remove('flash_message');
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * @inheritdoc
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @inheritdoc
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default=null)
    {
        if ($this->has($name) === false) {
            return $default;
        }

        return $_SESSION[$name];
    }
}