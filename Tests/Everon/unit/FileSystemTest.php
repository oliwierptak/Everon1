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

use Everon\Interfaces;

class FileSystemTest extends \Everon\TestCase
{
    
    public function setUp()
    {
        $path = [
            'this/is/test',
            'this/is',
            'this'
        ];
        
        $test_dir = $this->getTmpDirectory().$path[0];
        if (is_dir($test_dir)) {
            array_walk($path, function($item){
                rmdir($this->getTmpDirectory().$item);
            });
        }
    }
   
    public function testConstructor()
    {
        $FileSystem = new \Everon\Filesystem($this->getTmpDirectory());
        $this->assertInstanceOf('\Everon\Interfaces\FileSystem', $FileSystem);
        $this->assertEquals($this->getTmpDirectory(), $FileSystem->getRoot());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCreatePath(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test']);
        $FileSystem->createPath($path);
        $Dir = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($Dir->isDir());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testDeletePath(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test']);
        $FileSystem->createPath($path);
        $Dir = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($Dir->isDir());
        
        $FileSystem->deletePath($path);
        $this->assertFalse($Dir->isDir());
    }

    /**
     * @dataProvider dataProvider
     */    
    public function testCreate()
    {
        
    }
    
    public function dataProvider()
    {
        $FileSystem = new \Everon\Filesystem($this->getTmpDirectory());
        
        return [
            [$FileSystem]
        ];
    }

}