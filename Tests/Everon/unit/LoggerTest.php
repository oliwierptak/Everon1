<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

//todo: use chain of command to store log in different places: disk, memory, email, etc
class LoggerTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory());
        $this->assertInstanceOf('\Everon\Interfaces\Logger', $Logger);
    }

    protected function setUp()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory());
        
        foreach ($Logger->getLogFiles() as $level => $filename) {
            $log_file = $this->getLogDirectory().$filename;
            if (is_file($log_file)) {
                unlink($log_file);
            }
        }
    }

    public function testSetGetFiles()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory());
        $files = [
            'error' => 'test-everon-error.log',
        ];
        
        $Logger->setLogFiles($files);
        $this->assertCount(1, $Logger->getLogFiles());
    }

    public function testWriting()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory());
        $dates = [
            'warning' => $Logger->warn('warning'),
            'error' => $Logger->error('error'),
            'debug' => $Logger->debug('debug'),
            'trace' => $Logger->trace('trace')
        ];

        foreach ($Logger->getLogFiles() as $level => $filename) {
            $log_file = $this->getLogDirectory().$filename;
            $log_entry = $dates[$level]->format($Logger->getLogDateFormat()).' - '.$level;
            $this->assertEquals($log_entry."\n", file_get_contents($log_file));
        }
    }

}
