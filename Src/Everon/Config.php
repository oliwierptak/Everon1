<?php
namespace Everon;

use Everon\Helper;
use Everon\Exception;


class Config implements Interfaces\Config, Interfaces\Arrayable
{
    use Helper\ToArray;
    
    protected $name = null;

    protected $filename = '';

    /**
     * @var array
     */
    protected $go_path = [];


    /**
     * @param $name
     * @param $filename
     * @param \Closure|\Array $data
     */
    public function __construct($name, $filename, $data)
    {
        if (is_array($data) === false && is_callable($data) === false) {
            throw new Exception\Config('Invalid data type for: "%s@%s"', [$name, $filename]);
        }
        
        $this->name = $name;
        $this->filename = $filename;
        $this->data = $data;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        if ($this->data instanceof \Closure) {
            $this->data = $this->data->__invoke();
        }

        return $this->data;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default=null)
    {
        $data = $this->getData();
        
        if (empty($this->go_path) === false) {
            foreach ($this->go_path as $index) {
                if (isset($data[$index]) === false) {
                    $this->go_path = [];
                    return $default;
                }
                else {
                    $data = $data[$index];
                }
            }
        }
        
        $this->go_path = [];
        return isset($data[$name]) ? $data[$name] : $default;
    }

    /**
     * @param $where
     * @return Interfaces\Config
     */
    public function go($where)
    {
        $this->go_path[] = $where; 
        return $this;
    }
    
}