<?php
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

        if ($Previous instanceof \Exception) {
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
        if (strlen($class) != 0) {
            $message = $message.'{'.$class.'}';
        }
        if (strlen($message) != 0 && strlen($exception_message) != 0) {
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