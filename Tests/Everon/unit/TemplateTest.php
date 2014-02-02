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

class TemplateTest extends \Everon\TestCase
{
    use \Everon\Helper\Date;
    
    protected static $template_filename1 = '';
    protected static $template_filename2 = '';


    public function setupTemplateFile($filename, $content)
    {
        file_put_contents($filename, $content);
    }

    public function tearDown()
    {
        parent::tearDown();
        if (is_file(static::$template_filename1)) {
            unlink(static::$template_filename1);
        }
        if (is_file(static::$template_filename2)) {
            unlink(static::$template_filename2);
        }
    }

    /**
     * @dataProvider dataProvider
     */
    public function testConstructor()
    {
        $Template = new \Everon\View\Template('', ['world' => 'World']);
        $this->assertInstanceOf('\Everon\View\Template\Container', $Template);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetAndGetTemplateFilename(\Everon\View\Template $Template, $content, $output, $filename)
    {
        $this->setupTemplateFile($Template->getTemplateFile(), $content);
        $this->assertEquals($filename, $Template->getTemplateFile());
    }

    /**
     * @expectedException Everon\Exception\Template
     * @expectedExceptionMessage Template file: "wrong_file_name.htm" was not found
     */
    public function testValidateTemplateFilenameShouldThrownExceptionWhenFileWasNotFound()
    {
        $Template = new \Everon\View\Template('wrong_file_name.htm', []);
        $content = $Template->getTemplateContent();
    }

    public function dataProvider()
    {
        static::$template_filename1 = $this->getTmpDirectory().'template_1_'.$this->dateAsTime(time()).'.htm';
        static::$template_filename2 = $this->getTmpDirectory().'template_2_'.$this->dateAsTime(time()).'.htm';

        return [
            [new \Everon\View\Template(static::$template_filename1, ['test.world' => 'World']),
                'Hello {test.world}!',
                'Hello World!',
                static::$template_filename1
            ],
            [new \Everon\View\Template(static::$template_filename2, ['user.name' => 'John Doe']),
                'My name is <b>{user.name}</b>.',
                'My name is <b>John Doe</b>.',
                static::$template_filename2
            ]
        ];
    }


}