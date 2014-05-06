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

        if (is_numeric($expire_date) === false) {
            $this->setExpireDateFromString($expire_date);
        }
        else {
            $this->expire_date = $expire_date;
        }
    }

    /**
     * @inheritdoc
     */
    public function setExpireDateFromString($date_value='+15 minutes')
    {
        $this->expire_date = strtotime($date_value);
    }

    /**
     * @inheritdoc
     */
    public function neverExpire()
    {
        $this->setExpireDate(0);
    }
    
    /**
     * @inheritdoc
     */
    public function hasExpired()
    {
        $expires = (int) $this->expire_date;
        return $expires > 0 && $expires < time();
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @inheritdoc
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritdoc
     */
    public function setExpireDate($expire_date)
    {
        $this->expire_date = $expire_date;
    }

    /**
     * @inheritdoc
     */
    public function getExpireDate()
    {
        return $this->expire_date;
    }

    /**
     * @inheritdoc
     */
    public function setIsHttpOnly($is_http_only)
    {
        $this->is_http_only = $is_http_only;
    }

    /**
     * @inheritdoc
     */
    public function isHttpOnly()
    {
        return $this->is_http_only;
    }

    /**
     * @inheritdoc
     */
    public function setIsSecure($is_secure)
    {
        $this->is_secure = $is_secure;
    }

    /**
     * @inheritdoc
     */
    public function isSecure()
    {
        return $this->is_secure;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

}