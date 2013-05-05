<?php
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
    
    protected function getFilenameByLevel($level)
    {
        return $this->getLogDirectory().$this->log_files[$level];
    }

    /**
     * @param $message
     * @param $level
     * @param $parameters
     * @return \DateTime
     */
    protected function write($message, $level, $parameters)
    {
        $message = empty($parameters) === false ? vsprintf($message, $parameters) : $message;
        $StarDate = new \DateTime('@'.time());
        $filename = $this->getFilenameByLevel($level);
        $message = $StarDate->format($this->getLogDateFormat()).' - '.$message;
        error_log($message."\n", 3, $filename);
        
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
    
}