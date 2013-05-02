<?php
namespace Everon\Helper;


trait ToString
{

    protected $to_string = '';

    public function __toString()
    {
        try
        {
            if ($this->to_string === '' && method_exists($this, 'getToString')) {
                $this->to_string = $this->getToString();
            }

            return $this->to_string;
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

}
