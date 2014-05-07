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
    use Helper\Arrays;
    
    /**
     * @var string
     */
    protected $name = 'everon_cookie';

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
    
    protected $data_default = [
        'value' => null,
        'expire' => 0
    ];
    
    protected $data = [];


    /**
     * @param $name
     * @param $value
     * @param mixed $expire_date int as in 'time()' or string as in '+15 minutes'
     */
    function __construct($name, $value, $expire_date)
    {
        $this->name = $name;

        if (is_numeric($expire_date) === false) {
            $this->setExpireDateFromString($expire_date);
        }
        else {
            $this->setExpire($expire_date);
        }
        
        $this->setDataFromJsonOrValue($value);
    }

    /**
     * @inheritdoc
     */
    public function setExpireDateFromString($date_value)
    {
        $this->setExpire(strtotime($date_value));
    }

    /**
     * @inheritdoc
     */
    public function neverExpire()
    {
        $this->setExpireDateFromString('+5 years');
    }
    
    /**
     * @inheritdoc
     */
    public function isExpired()
    {
        $expires = (int) $this->getExpire();
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
    public function setExpire($expire_date)
    {
        $this->data['expire'] = $expire_date;
    }

    /**
     * @inheritdoc
     */
    public function getExpire()
    {
        return $this->data['expire'];
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
        $this->data['value'] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->data['value'];
    }

    /**
     * @return string
     */
    public function getJsonValue()
    {
        return json_encode($this->data);
    }

    /**
     * @param $json_value string
     */
    public function setDataFromJsonOrValue($json_value)
    {
        $json_value = trim((string) $json_value);
        if ($json_value !== '' && $json_value[0] === '{') {
            $this->data = $this->arrayMergeDefault($this->data_default, json_decode($json_value, true));
        }
        else {
            $this->data = $this->arrayMergeDefault($this->data_default, [
                'value' => $json_value
            ]);
        }
    }

    public function delete()
    {
        $this->setExpireDateFromString('-1 year');
    }
}