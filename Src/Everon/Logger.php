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
    use Helper\Date;
    
    protected $log_files = [
        'error' => 'everon-error.log',
        'warning' => 'everon-warning.log',
        'debug' => 'everon-debug.log',
        'trace' => 'everon-trace.log',
    ];
    
    protected $log_directory = null;
    
    
    public function __construct($directory)
    {
        $this->setLogDirectory($directory);
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
     * @param $message
     * @param $level
     * @param $parameters
     * @return \DateTime
     * @throws Exception\Logger
     */
    protected function write($message, $level, $parameters)
    {
        $Filename = $this->getFilenameByLevel($level);
        $Dir = new \SplFileInfo($Filename->getPath());
        
        if ($Dir->isWritable() === false) {
            throw new Exception\Logger('Unable to write to log file: "%s"', [$Filename->getPathname()]);
        }

        $message = empty($parameters) === false ? vsprintf($message, $parameters) : $message;
        $StarDate = new \DateTime('@'.time());
        $message = $StarDate->format($this->getLogDateFormat()).' - '.$message;
        
        error_log($message."\n", 3, $Filename->getPathname());
        
        return $StarDate;
    }
    
    public function getLogDateFormat()
    {
        return 'Y-m-d@H:i:s';
    }

    public function setLogDirectory($directory)
    {
        $this->log_directory = $directory;
    }

    public function getLogDirectory()
    {
        return $this->log_directory;
    }    
    
    public function setLogFiles(array $files)
    {
        $this->log_files = $files;
    }
    
    public function getLogFiles()
    {
        return $this->log_files;
    }

    public function warn($message, $parameters=[])
    {
        return $this->write($message, 'warning', $parameters);
    }
    
    public function trace($message, $parameters=[])
    {
        return $this->write($message, 'trace', $parameters);
    }
    
    public function error($message, $parameters=[])
    {
        return $this->write($message, 'error', $parameters);
    }
    
    public function debug($message, $parameters=[])
    {
        return $this->write($message, 'debug', $parameters);
    }

    /**
     * eg. $this->getLogger()->auth(...)  will log to logs/auth.log
     * eg. $this->getLogger()->test_me(...)  will log to logs/test_me.log
     * 
     * @param $name
     * @param $arguments
     * @return \DateTime
     */
    public function __call($name, array $arguments=[])
    {
        $name = escapeshellarg(preg_replace('/[^a-z0-9_]/i', '', $name));
        @list($message, $parameters) = $arguments;
        return $this->write($message, $name, $parameters);
    }
    
}