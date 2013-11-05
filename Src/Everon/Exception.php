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
    use Helper\ToString;

    protected $toString = null;


    /**
     * @param string|\Exception $message
     * @param null|array $params
     * @param null|\Exception $Previous
     */
    public function __construct($message, $params=null, $Previous=null)
    {
        $message = $this->formatExceptionParams($message, $params);
        if ($message instanceof \Exception) {
            $message = $message->getMessage();
        }
        else if ($Previous instanceof \Exception) { //else to avoid displaying duplicated error messages
            $message .= $this->formatExceptionParams(".\n%s", $Previous->getMessage());
        }

        parent::__construct($message, 0, $Previous);
    }

    /**
     * @param $message
     * @param $parameters
     * @return string
     */
    protected function formatExceptionParams($message, $parameters)
    {
        if (trim($message) == '' || is_null($parameters)) {
            return $message;
        }

        if (is_array($parameters) === false) {
            $parameters = array($parameters);
        }

        return vsprintf($message, $parameters);
    }

    /**
     * @param string $exception_class
     * @param string $message
     * @param mixed $value
     * @throws \Exception
     */
    protected function throwException($exception_class, $message, $value)
    {
        try {
            $class = $exception_class;
            if (class_exists($class, true) === false) {
                $exception_class = 'Everon\Exception\Asserts';
            }
        }
        catch (\Exception $e) {
            try {
                $class = "Everon\\Exception\\${exception_class}";
                if (class_exists($class, true) === false) {
                    $exception_class = 'Everon\Exception\Asserts';
                }
                else {
                    $exception_class = $class;
                }
            }
            catch (\Exception $e) {
                $exception_class = 'Everon\Exception\Asserts';
            }
        }

        throw new $exception_class($message, $value);
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
        return self::getErrorMessageFromException($this);
    }


}