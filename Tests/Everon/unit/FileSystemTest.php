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
            'var/this/is/test/example.txt',
            'var/this/is/test/',
            'var/this/is/',
            'var/this/',
            'var/',
            'this/is/test/more',
            'this/is/test/example.txt',
            'this/is/test',
            'this/is',
            'this'
        ];
        
        array_walk($path, function($item){
            $resource = $this->getTmpDirectory().$item;
            if (is_file($resource)) {
                @unlink($resource);
            }
            
            if (is_dir($resource))
                @rmdir($resource);
        });

        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test', 'example.txt']);
        @unlink($path);
    }
   
    public function testConstructor()
    {
        $FileSystem = new \Everon\FileSystem($this->getTmpDirectory());
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

        $FileSystem->createPath($path.DIRECTORY_SEPARATOR.'more');
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
    public function testSave(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($File->isFile());
        
        $content = file_get_contents($File->getPathname());
        $this->assertEquals($content, 'filesystem test');
    }
    
    /**
     * @dataProvider dataProvider
     */    
    public function testLoad(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($File->isFile());
        
        $content = file_get_contents($File->getPathname());
        $this->assertEquals($content, $FileSystem->load($path));
    }
    
    /**
     * @dataProvider dataProvider
     */    
    public function testDelete(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($File->isFile());
        
        $FileSystem->delete($path);
        $this->assertFalse($File->isFile());
    }
    
    /**
     * @dataProvider dataProvider
     */    
    public function testVirtualRoot(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['//this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($File->isFile());
    }
    
    /**
     * @dataProvider dataProvider
     */    
    public function testCreateOutsideRoot(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['/var','this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($this->getTmpDirectory().$path);
        $this->assertTrue($File->isFile());
    }
    
    /**
     * @dataProvider dataProvider
     */    
    public function testCreateLeadingSlash(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['/this', 'is', 'test', 'example.txt']);
        $FileSystem->save($path, 'filesystem test');
        $File = new \SplFileInfo($path);
        $this->assertFalse($File->isFile());
    }

    /**
     * @dataProvider dataProvider
     */    
    public function testListPath(Interfaces\FileSystem $FileSystem)
    {
        $path = implode(DIRECTORY_SEPARATOR, ['this', 'is', 'test']);
        $FileSystem->save($path.DIRECTORY_SEPARATOR.'example.txt', 'filesystem test');        
        $files = $FileSystem->listPath($path);
        $this->assertCount(1, $files);
        $this->assertEquals('example.txt', $files[0]->getBasename());
    }
    
    public function dataProvider()
    {
        $FileSystem = new \Everon\FileSystem($this->getTmpDirectory());
        
        return [
            [$FileSystem]
        ];
    }

}