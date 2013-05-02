<?php
namespace Everon\Test;

class TemplateCompilerCurlyTest extends \Everon\TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testCompile($content, $data, $expected)
    {
        $Curly = new \Everon\View\Template\Compiler\Curly();
        
        $compiled = $Curly->compile($content, $data);
        
        $this->assertInstanceOf('\Everon\Interfaces\TemplateCompiler', $Curly);
        $this->assertEquals($expected, $compiled);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testCompileWithNestedArrays($content, $data, $expected)
    {
        $Curly = new \Everon\View\Template\Compiler\Curly();
        
        $data['user'] = [
            'data' => [
                'session' => [
                    'logged'=>'maybe'
                ]
            ]
        ];
        $compiled = $Curly->compile($content, $data);
        $this->assertEquals($expected, $compiled);
        
        $data['user'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];        
        $compiled = $Curly->compile($content, $data);
        $this->assertEquals($expected, $compiled);
    }

    public function dataProvider()
    {
        return [
            ['Hello {world}!', ['world' => 'World'], 'Hello World!']
        ];
    }


}