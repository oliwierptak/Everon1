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
        $value = ($value === null) ? $this->getFactory()->buildDateTime() : $value;
        //return (new \DateTime($value->format(\DateTime::ATOM), $TimeZone ?: (new \DateTimeZone(date_default_timezone_get()))))->format($format);
        $TimeZone = $TimeZone === null ? (new \DateTimeZone(date_default_timezone_get())) : $TimeZone;
        $DateTime = $this->getFactory()->buildDateTime($value->format(\DateTime::ATOM), $TimeZone);
        return $DateTime->format($format);
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
        $Tz = $this->getFactory()->buildDateTimeZone($timezone);
        $TzToConvert = ($timezone_to_convert !== null) ? $this->getFactory()->buildDateTime($timezone_to_convert) : null;
        $Date =  $this->getFactory()->buildDateTime($value, $TzToConvert ?: $Tz);
        $Date->setTimezone($TzToConvert ?: $Tz); //force datetime_format to 3, you can use getOffset() if you want to get gmt
  
        return $Date;
    }
}