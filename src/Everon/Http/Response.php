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
    /**
     * @var Interfaces\HeaderCollection
     */
    protected $HeaderCollection = null;
    
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

    
    public function __construct($guid, Interfaces\HeaderCollection $Headers)
    {
        parent::__construct($guid);
        $this->HeaderCollection = $Headers;
    }
    
    protected function sendHeaders()
    {
        $this->HeaderCollection->set('HTTP/1.1 '.$this->status_code, '');
        $this->HeaderCollection->set('EVRID', $this->guid);
        
        foreach ($this->HeaderCollection as $name => $value) {
            if (trim($value) !== '') {
                header($name.': '.$value, false);
            }
            else {
                header($name, false);
            }
        }
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }


    public function addHeader($name, $value)
    {
        $this->HeaderCollection->set($name, $value);
    }

    public function getHeader($name)
    {
        $this->HeaderCollection->get($name);
    }

    /**
     * @return HeaderCollection
     */
    public function getHeaderCollection()
    {
        return $this->HeaderCollection;
    }

    /**
     * @param Interfaces\HeaderCollection $Collection
     */
    public function setHeaderCollection(Interfaces\HeaderCollection $Collection)
    {
        $this->HeaderCollection = $Collection;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function setStatusCode($status)
    {
        $this->status_code = (int) $status;
    }

    public function getStatusMessage()
    {
        return $this->status_message;
    }

    /**
     * @param string $status_message
     */
    public function setStatusMessage($status_message)
    {
        $this->status_message = $status_message;
    }

    public function toHtml()
    {
        $this->setContentType('text/html');
        $this->addHeader('content-type', 'text/html; charset="'.$this->getCharset().'"');
        $this->send();
        return (string) $this->data;
    }

    public function toJson($root='data')
    {
        $this->setContentType('application/json');
        $json = parent::toJson($root);
        $this->addHeader('content-type', 'application/json');
        $this->send();
        return $json;
    }

    public function toText()
    {
        $this->setContentType('text/plain');
        $this->addHeader('content-type', 'text/plain; charset="'.$this->getCharset().'"');
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