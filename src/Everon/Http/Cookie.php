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
use Everon\Http\Interfaces;

class Cookie implements Interfaces\Cookie
{
    /**
     * @var string
     */
    protected $name = 'everon_cookie';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var int
     */
    protected $expire_date = null;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var string
     */
    protected $domain = null;

    /**
     * @var bool
     */
    protected $is_secure = false;

    /**
     * @var bool
     */
    protected $is_http_only = true;


    /**
     * @param $name
     * @param $value
     * @param mixed $expire_date int as in 'time()' or string as in '+15 minutes'
     */
    function __construct($name, $value, $expire_date)
    {
        $this->name = $name;
        $this->value = $value;
        
        if (is_numeric($expire_date)) {
            $this->setExpireDateFromString($expire_date);
        }
        else {
            $this->expire_date = $expire_date;
        }
    }

    public function setExpireDateFromString($date_value='+15 minutes')
    {
        $this->expire_date = strtotime($date_value);
    }

    public function hasExpired()
    {
        $expires = (int) $this->expire_date;
        return $expires > 0 && $expires < time();
    }
    
    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param int $expire_date
     */
    public function setExpireDate($expire_date)
    {
        $this->expire_date = $expire_date;
    }

    /**
     * @return int
     */
    public function getExpireDate()
    {
        return $this->expire_date;
    }

    /**
     * @param boolean $is_http_only
     */
    public function setIsHttpOnly($is_http_only)
    {
        $this->is_http_only = $is_http_only;
    }

    /**
     * @return boolean
     */
    public function IsHttpOnly()
    {
        return $this->is_http_only;
    }

    /**
     * @param boolean $is_secure
     */
    public function setIsSecure($is_secure)
    {
        $this->is_secure = $is_secure;
    }

    /**
     * @return boolean
     */
    public function IsSecure()
    {
        return $this->is_secure;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}