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

class Response implements Interfaces\Response
{
    protected $data = null;
    protected $HeaderCollection = null;
    protected $content_type = 'text/html';
    protected $charset = 'utf-8';
    protected $status = 200;
    protected $result = false;

    
    public function __construct($data, Interfaces\Collection $Headers)
    {
        $this->data = $data;
        $this->HeaderCollection = $Headers;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    public function setContentType($content_type)
    {
        $this->content_type = strtolower($content_type);
    }
    
    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = (int) $status;
    }
    
    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = (bool) $result;
    }
    
    protected function sendHeaders()
    {
        if ($this->HeaderCollection->has('content-type') === false) {
            switch ($this->getContentType()) {
                case 'application/json':
                    $this->HeaderCollection->set('content-type', 'application/json');
                    break;

                case 'text/html':
                    $this->HeaderCollection->set('content-type', 'text/html; charset="'.$this->getCharset().'"');
                    break;
            }
        }

        foreach ($this->HeaderCollection as $name => $value) {
            header($name.': '.$value, false);
        }
    }
    
    /**
     * @return Http\HeaderCollection
     */
    public function getHeaderCollection()
    {
        return $this->HeaderCollection;
    }

    public function toHtml()
    {
        $this->setContentType('text/html');
        return is_array($this->data) ? implode('', $this->data) : $this->data;
    }

    public function toJson($root='data')
    {
        $this->setContentType('application/json');
        return json_encode([$root=>$this->data]);
    }
    
    public function send()
    {
        $this->sendHeaders();
    }

}