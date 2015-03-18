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

class Logger implements Interfaces\Logger
{
    use Dependency\Injection\Factory;
    
    use Helper\DateFormatter;
    
    
    protected $log_files = [];
    
    protected $log_directory = null;
    
    protected $log_request_identifier = null;
    
    protected $enabled = false;
    
    protected $write_count = 0;
    

    /**
     * @param $directory
     * @param $enabled
     */
    public function __construct($directory, $enabled)
    {
        $this->log_directory = $directory;
        $this->enabled = (bool) $enabled;
    }
    
    public function setRequestIdentifier($request_identifier)
    {
        $this->log_request_identifier = $request_identifier;
    }
    
    public function getRequestIdentifier()
    {
        return $this->log_request_identifier;
    }    

    /**
     * @param $level
     * @return \SplFileInfo
     */
    protected function getFilenameByLevel($level)
    {
        if (array_key_exists($level, $this->log_files) === false) {
            return new \SplFileInfo($this->getLogDirectory().$level.'.log'); //eg. logs/login.log 
        }
        
        return new \SplFileInfo($this->getLogDirectory().$this->log_files[$level]);
    }

    /**
     * Don't generate errors and only write to log file when possible
     * 
     * @param $message \Exception or string
     * @param $level
     * @param $parameters
     * @return \DateTime
     * @throws Exception\Logger
     */
    protected function write($message, $level, array $parameters)
    {
        if ($this->enabled === false) {
            return null;
        }
        
        if ($this->log_request_identifier === null) {
            $this->log_request_identifier = "MISSING_REQUEST_ID";
        }

        $ExceptionToTrace = $message;
        if ($message instanceof \Exception) {
            $message = (string) $message; //casting to string will append exception name
        }
        
        $LogDate = ($this->getFactory()->buildDateTime(null, $this->getLogDateTimeZone()));
        $Filename = $this->getFilenameByLevel($level);
        $Dir = new \SplFileInfo($Filename->getPath());
        
        if ($Dir->isWritable()) {
            if ($Filename->isFile() && $Filename->isWritable() === false) {
                return $LogDate;
            }
            
            $this->logRotate($Filename);
            
            $this->write_count++;
            $request_id = substr($this->log_request_identifier, 0, 6);
            $trace_id =  substr(md5(uniqid()), 0, 6);
            $write_count = $this->write_count;
            $id = "$request_id/$trace_id ($write_count)";
            
            $message = empty($parameters) === false ? vsprintf($message, $parameters) : $message;
            $message = $LogDate->format($this->getLogDateFormat())." ${id} ".$message;

            $trace = null;
            if ($ExceptionToTrace instanceof \Everon\Exception) {
                $trace = $ExceptionToTrace->getLoggedTrace();
            }
            else if ($ExceptionToTrace instanceof \Exception) {
                $trace = $ExceptionToTrace->getTraceAsString();
            }

            if ($trace !== null) {
                $message .= "\n".$LogDate->format($this->getLogDateFormat())." ${id} \n".$trace."\n";
            }

            error_log($message."\n", 3, $Filename->getPathname());
        }
        
        return $LogDate;
    }

    /**
     * @param \SplFileInfo $Filename
     */
    protected function logRotate(\SplFileInfo $Filename)
    {
        if ($Filename->isFile() === false) {
            return;
        }
        
        $size = $Filename->getSize();
        $size = intval($size / 1024);
        
        //reset the log file if its size exceeded 512 KB
        if ($size > 512) { //KB, todo: read it from config
            $h = fopen($Filename->getPathname(), 'w');
            fwrite($h, '');
            fclose($h);
        }
    }

    /**
     * @inheritdoc
     */
    public function getLogDateFormat()
    {
        return 'c';
    }

    /**
     * @inheritdoc
     */
    public function getLogDateTimeZone()
    {
        $timezone = @date_default_timezone_get();
        $timezone = $timezone ?: 'Europe/Amsterdam'; //todo: visit coffeeshop
        return $this->getFactory()->buildDateTimeZone($timezone);
    }

    /**
     * @inheritdoc
     */
    public function setLogDirectory($directory)
    {
        $this->log_directory = $directory;
    }

    /**
     * @inheritdoc
     */
    public function getLogDirectory()
    {
        return $this->log_directory;
    }

    /**
     * @inheritdoc
     */
    public function setLogFiles(array $files)
    {
        $this->log_files = $files;
    }

    /**
     * @inheritdoc
     */
    public function getLogFiles()
    {
        return $this->log_files;
    }

    /**
     * @inheritdoc
     */
    public function warn($message, array $parameters=[])
    {
        return $this->write($message, 'warning', $parameters);
    }

    /**
     * @inheritdoc
     */
    public function trace(\Exception $Message, array $parameters=[])
    {
        $Message = $Message->getTraceAsString();
        return $this->write($Message, 'trace', $parameters);
    }

    /**
     * @inheritdoc
     */
    public function error($message, array $parameters=[])
    {
        return $this->write($message, 'error', $parameters);
    }

    /**
     * @inheritdoc
     */
    public function debug($message, array $parameters=[])
    {
        return $this->write($message, 'debug', $parameters);
    }

    /**
     * @inheritdoc
     */
    public function log($log_name, $message, array $parameters=[])
    {
        return $this->write($message, $log_name, $parameters);
    }

    /**
     * @param $log_name
     */
    public function logReset($log_name)
    {
        try {
            $Filename = $this->getFilenameByLevel($log_name);
            $h = fopen($Filename->getPathname(), 'w');
            fwrite($h, '');
            fclose($h);            
        }
        catch (\Exception $e) {
        }
    }

    /**
     * $this->getLogger()->auth(...)  will log to logs/auth.log
     * 
     * @param $name
     * @param $arguments
     * @return \DateTime
     */
    /*public function __call($name, array $arguments=[])
    {
        if ($this->enabled === false) {
            return null;
        }
        
        $name = escapeshellarg(preg_replace('/[^a-z0-9_]/i', '', $name));
        $name = str_replace(['"', "'"], '', $name);

        @list($message, $parameters) = $arguments;
        $parameters = $parameters ?: [];
        return $this->write($message, $name, $parameters);
    }*/
    
}