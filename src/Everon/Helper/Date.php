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
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateFormat($timestamp, $format, \DateTimeZone $TimeZone=null)
    {
        return (new \DateTime('@'.$timestamp, $TimeZone ?: (new \DateTimeZone(date_default_timezone_get()))))->format($format);
    }

    /**
     * @param $timestamp
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateAsMysql($timestamp, \DateTimeZone $TimeZone=null)
    {
        return $this->dateFormat($timestamp, 'Y-m-d@H:i:s', $TimeZone);
    }

    /**
     * @param $timestamp
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateAsTime($timestamp, \DateTimeZone $TimeZone=null)
    {
        return $this->dateFormat($timestamp, 'His', $TimeZone);
    }
}