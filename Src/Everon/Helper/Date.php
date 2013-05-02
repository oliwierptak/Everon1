<?php
namespace Everon\Helper;

trait Date
{
    /**
     * @param $timestamp
     * @param $format
     * @return string
     */
    protected function dateFormat($timestamp, $format)
    {
        return (new \DateTime('@'.$timestamp))->format($format);
    }

    /**
     * @param integer $timestamp
     * @return string
     */
    public function dateAsMysql($timestamp)
    {
        return $this->dateFormat($timestamp, 'Y-m-d@H:i:s');
    }

    /**
     * @param integer $timestamp
     * @return string
     */
    public function dateAsTime($timestamp)
    {
        return $this->dateFormat($timestamp, 'His');
    }
}