<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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