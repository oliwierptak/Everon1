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

class ConfigExpressionMatcherTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Matcher = new \Everon\Config\ExpressionMatcher();
        $this->assertInstanceOf('\Everon\Interfaces\ConfigExpressionMatcher', $Matcher);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetCompiler(Interfaces\ConfigExpressionMatcher $Matcher)
    {
        $Config = $this->getMock('Everon\Interfaces\Config');
        $Config->expects($this->any())
            ->method('get')
            ->with('url')
            ->will($this->returnValue('/testme'));

        $Manager = $this->getMock('Everon\Interfaces\ConfigManager');
        $Manager->expects($this->any())
            ->method('getConfigByName')
            ->will($this->returnValue($Config));

        $Compiler = $Matcher->createCompiler($Manager);

        $data = ['this_is_my_url' => '%application.env.url%'];
        $Compiler($data);
        $this->assertEquals($data, ['this_is_my_url' => '/testme']);

        $data = [];
        $Compiler($data);
        $this->assertEquals($data, []);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetters(Interfaces\ConfigExpressionMatcher $Matcher)
    {
        $Matcher->setExpressions(['%test.me%']);
        $this->assertEquals(['%test.me%'], $Matcher->getExpressions());
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->getFactory();
        
        $Matcher = $Factory->buildConfigExpressionMatcher();

        return [
            [$Matcher]
        ];
    }

}
