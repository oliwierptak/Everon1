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


abstract class Exception extends \Exception
{
    use Helper\Exceptions;
    use Helper\ToString;

    protected $toString = null;
    
    protected $logged_trace = null;


    /**
     * @param string $message
     * @param null $params
     * @param null $Previous
     * @param null $Callback
     */
    public function __construct($message, $params=null, $Previous=null, $Callback=null)
    {
        if ($message instanceof \Exception) {
            $message = $message->getMessage();
        }
        
        $message = $this->formatExceptionParams($message, $params);

        if ($Callback instanceof \Closure) {
            $Callback();
        }
        
        parent::__construct($message, 0, $Previous);

        if ($Previous instanceof \Exception) {
            $this->logged_trace = $Previous->getTraceAsString();
        }
    }

    /**
     * @return string
     */
    public function getLoggedTrace()
    {
        return $this->logged_trace ?: $this->getTraceAsString();
    }
    
    /**
     * @param \Exception $Exception
     * @return string
     */
    public static function getErrorMessageFromException(\Exception $Exception)
    {
        $message = "";
        $exception_message = $Exception->getMessage();
        $class = get_class($Exception);
        if ($class != '') {
            $message = $message.'{'.$class.'}';
        }
        if ($message != '' && $exception_message != '') {
            $message = $message.' ';
        }
        $message = $message.$exception_message;

        return $message;
    }

    /**
     * @return string
     */
    protected function getToString()
    {
        return static::getErrorMessageFromException($this);
    }

}