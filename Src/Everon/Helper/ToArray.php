<?php
namespace Everon\Helper;


trait ToArray
{

    /**
     * @var array
     */
    protected $data = [];


    /**
     * array|stdClass $this->data is declared in class which uses this trait
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->data;
        if ($data instanceof \Closure) {
            $data = $data->__invoke();
        }
        
        foreach ($data as $key => $value ) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $data[$key] = $value->toArray();
            }
        }
        
        return $data;
    }

}
