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

trait DateFormatter
{
    /**
     * @param $value
     * @param $format
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateFormat($value=null, $format, \DateTimeZone $TimeZone=null)
    {
        return (new \DateTime($value->format(\DateTime::ATOM), $TimeZone ?: (new \DateTimeZone(date_default_timezone_get()))))->format($format);
    }

    /**
     * @param $value
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateAsMysql($value=null, \DateTimeZone $TimeZone=null)
    {
        return $this->dateFormat($value, 'Y-m-d@H:i:s', $TimeZone);
    }

    /**
     * @param $value
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateAsPostgreSql($value=null, \DateTimeZone $TimeZone=null)
    {
        return $this->dateFormat($value, 'Y-m-d H:i:s.uP', $TimeZone);
    }

    /**
     * @param $value
     * @param \DateTimeZone $TimeZone
     * @return string
     */
    protected function dateAsTime($value=null, \DateTimeZone $TimeZone=null)
    {
        return $this->dateFormat($value, 'His', $TimeZone);
    }

    /**
     * @param $value
     * @param $timezone
     * @param null $timezone_to_convert
     * @return \DateTime
     */
    protected function getDateTime($value=null, $timezone, $timezone_to_convert=null) 
    {
        $Tz = new \DateTimeZone($timezone);
        $TzToConvert = ($timezone_to_convert !== null) ? new \DateTimeZone($timezone_to_convert) : null;
        $Date =  new \DateTime($value, $TzToConvert ?: $Tz);
        $Date->setTimezone($TzToConvert ?: $Tz); //force datetime_format to 3, you can use getOffset() if you want to get gmt
          
        return $Date;
    }
}