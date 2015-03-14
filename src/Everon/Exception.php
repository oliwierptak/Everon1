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


    /**
     * @param string $message
     * @param null $params
     * @param null $Previous
     * @param null $Callback
     */
    public function __construct($message, $params=null, $Previous=null, $Callback=null)
    {
        $message = $this->formatExceptionParams($message, $params);
        if ($message instanceof \Exception) {
            $message = $message->getMessage();
        }
        else if ($Previous instanceof \Exception) { //avoid displaying duplicated error messages
            if (trim($message) !== '') {
                $message .= '.';
            }
            $message .= $this->formatExceptionParams("\n%s", $Previous->getMessage());
        }

        if ($Callback instanceof \Closure) {
            $Callback();
        }
        
        parent::__construct($message, 0, $Previous);
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