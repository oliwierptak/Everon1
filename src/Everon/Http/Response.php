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

use Everon\Dependency;
use Everon\Http\Interfaces;
use Everon\Response as BasicResponse;

class Response extends BasicResponse implements Interfaces\Response
{
    use Dependency\Injection\Factory;
    
    /**
     * @var Interfaces\HeaderCollection
     */
    protected $HeaderCollection = null;

    /**
     * @var Interfaces\CookieCollection
     */
    protected $CookieCollection = null;
    
    protected $content_type = 'text/plain';

    /**
     * @var int
     */
    protected $content_length = 0;
    
    protected $charset = 'utf-8';

    /**
     * @var int
     */
    protected $status_code = 200;
    
    protected $status_message = 'OK';


    /**
     * @param $guid
     * @param Interfaces\HeaderCollection $Headers
     * @param Interfaces\CookieCollection $CookieCollection
     */
    public function __construct($guid, Interfaces\HeaderCollection $Headers, Interfaces\CookieCollection $CookieCollection)
    {
        parent::__construct($guid);
        $this->HeaderCollection = $Headers;
        $this->CookieCollection = $CookieCollection;
    }
    
    protected function sendHeaders()
    {
        $this->HeaderCollection->set('HTTP/1.1 '.$this->status_code, '');
        $this->HeaderCollection->set('EVRID', $this->guid);

        /**
         * @var \Everon\Http\Interfaces\Cookie $Cookie
         */
        foreach ($this->CookieCollection as $name => $Cookie) {
            setcookie(
                $Cookie->getName(),
                $Cookie->getValue(),
                $Cookie->getExpireDate(),
                $Cookie->getPath(),
                $Cookie->getDomain(),
                $Cookie->isSecure(),
                $Cookie->isHttpOnly()
            );
        }
        
        foreach ($this->HeaderCollection as $name => $value) {
            if (trim($value) !== '') {
                header($name.': '.$value, false);
            }
            else {
                header($name, false);
            }
        }
    }

    /**
     * @param \Everon\Http\Interfaces\CookieCollection $CookieCollection
     */
    public function setCookieCollection($CookieCollection)
    {
        $this->CookieCollection = $CookieCollection;
    }

    /**
     * @return \Everon\Http\Interfaces\CookieCollection
     */
    public function getCookieCollection()
    {
        return $this->CookieCollection;
    }
    
    /**
     * @param Interfaces\Cookie $Cookie
     */
    public function addCookie(Interfaces\Cookie $Cookie)
    {
        $this->CookieCollection->set($Cookie->getName(), $Cookie);
    }

    /**
     * @param Interfaces\Cookie $Cookie
     */
    public function deleteCookie(Interfaces\Cookie $Cookie)
    {
        $this->deleteCookieByName($Cookie->getName());
    }

    /**
     * @param Interfaces\Cookie $name
     */
    public function deleteCookieByName($name)
    {
        $Cookie = $this->getCookie($name);
        if ($Cookie !== null) {
            $Cookie->delete();
        }
        else {
            $Cookie = $this->getFactory()->buildHttpCookie($name, '', time());
            $Cookie->delete();
        }
        
        $this->CookieCollection->set($Cookie->getName(), $Cookie);
    }

    /**
     * @param $name
     * @return Interfaces\Cookie|null
     */
    public function getCookie($name)
    {
        return $this->CookieCollection->get($name, null);
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @inheritdoc
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @inheritdoc
     */
    public function setHeader($name, $value)
    {
        $this->HeaderCollection->set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        $this->HeaderCollection->get($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderCollection()
    {
        return $this->HeaderCollection;
    }

    /**
     * @inheritdoc
     */
    public function setHeaderCollection(Interfaces\HeaderCollection $Collection)
    {
        $this->HeaderCollection = $Collection;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @inheritdoc
     */
    public function setStatusCode($status)
    {
        $this->status_code = (int) $status;
    }

    public function getStatusMessage()
    {
        return $this->status_message;
    }

    /**
     * @inheritdoc
     */
    public function setStatusMessage($status_message)
    {
        $this->status_message = $status_message;
    }

    public function toHtml()
    {
        $this->setContentType('text/html');
        $this->setHeader('content-type', 'text/html; charset="'.$this->getCharset().'"');
        $this->send();
        return (string) $this->data;
    }

    /**
     * @inheritdoc
     */
    public function toJson()
    {
        $this->setContentType('application/json');
        $json = parent::toJson();
        $this->setHeader('content-type', 'application/json');
        $this->send();
        return $json;
    }

    public function toText()
    {
        $this->setContentType('text/plain');
        $this->setHeader('content-type', 'text/plain; charset="'.$this->getCharset().'"');
        $text = parent::toText();
        $this->send();
        return (string) $text;
    }

    public function send()
    {
        if (headers_sent() === false) {
            $this->sendHeaders();
        }
    }
}