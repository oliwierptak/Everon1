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
    use Helper\Asserts;
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
     * @param \Exception $e
     * @return string
     */
    public static function getErrorMessageFromException(\Exception $e)
    {
        $message = "";
        $exception_message = $e->getMessage();
        $class = get_class($e);
        if ($class != '') {
            $message = $message.'{'.$class.'}';
        }
        if ($message != '' && $exception_message != '') {
            $message = $message . " ";
        }
        $message = $message . $e->getMessage();

        return $message;
    }

    /**
     * @return string
     */
    public function getToString()
    {
        return self::getErrorMessageFromException($this);
    }


}