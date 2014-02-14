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
    protected function sendHeaders()
    {
        header('HTTP/1.1 '.$this->status);
        header('EVRID:'. $this->guid);
        foreach ($this->HeaderCollection as $name => $value) {
            header($name.': '.$value, false);
        }
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

    public function toHtml()
    {
        $this->setContentType('text/html');
        $this->addHeader('content-type', 'text/html; charset="'.$this->getCharset().'"');
        $this->send();
        return (string) $this->data;
    }

    public function toJson($root='data')
    {
        $json = parent::toJson($root);
        $this->addHeader('content-type', 'application/json');
        $this->send();
        return $json;
    }

    public function toText()
    {
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

    public function addHeader($name, $value)
    {
        $this->HeaderCollection->set($name, $value);
    }

    public function getHeader($name)
    {
        $this->HeaderCollection->get($name);
    }
}