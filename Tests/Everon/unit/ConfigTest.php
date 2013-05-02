<?php
namespace Everon\Test;

class ConfigTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Config = new \Everon\Config('test', 'test.ini', ['test' => 'true']);
        $this->assertInstanceOf('\Everon\Interfaces\Config', $Config);
    }
    
    public function testGetDataShouldInvokeClosureWhenNeeded()
    {
        $Config = new \Everon\Config('test', 'test.ini', function() {
            return ['test' => true];
        });
        $this->assertTrue($Config->get('test'));
    }

    /**
     * @expectedException \Everon\Exception\Config 
     * @expectedExceptionMessage Invalid data type for: "test@test.ini"
     */
    public function testConstructorShouldThrowExceptionWhenDataNotArrayOrClosure()
    {
        $Config = new \Everon\Config('test', 'test.ini', null);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Interfaces\Config $Config)
    {
        $Config->setFilename('test.ini');
        $Config->setName('test');
        
        $this->assertEquals('test.ini', $Config->getFilename());
        $this->assertEquals('test', $Config->getName());

        $this->assertEquals('yes, this is test', $Config->go('test')->get('halo'));
        $this->assertEquals('really_deep', $Config->go('another_test')->go('goodbye')->go('this')->go('is')->get('getting'));
        $this->assertEquals(['halo' => 'yes, this is test'], $Config->get('test'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetNonExistingValueShouldReturnNullOrDefault(\Everon\Interfaces\Config $Config)
    {
        $this->assertEquals('yes, this is test', $Config->go('test')->get('halo'));
        $this->assertEquals(null, $Config->get('errorrrr'));
        $this->assertEquals(null, $Config->go('errorrrr')->get('more error'));
        $this->assertEquals('default', $Config->get('errorrrr', 'default'));
    }

    public function dataProvider()
    {
        $Config = new \Everon\Config('test', 'test.ini', [
            'test' => [
                'halo' => 'yes, this is test'
            ],
            'another_test' => [
                'goodbye' => [ 
                    'bye' => 'now',
                    'this' => [
                        'is' => ['getting' => 'really_deep']
                    ]
                ] 
            ]
        ]);
        
        return [
            [$Config]
        ];
    }

}
