<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Config;

use Everon\Interfaces;

class ExpressionMatcherTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Matcher = new \Everon\Config\ExpressionMatcher();
        $this->assertInstanceOf('Everon\Interfaces\ConfigExpressionMatcher', $Matcher);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCreateCompilerAndCompile(Interfaces\ConfigExpressionMatcher $Matcher, array $data)
    {
        $data['application']['env']['url'] = '/testme';
        $data['test']['config']['url'] = '%application.env.url%';
        $Compiler = $Matcher->getCompiler($data);
        $Compiler($data);        
        $this->assertEquals($data['test']['config']['url'], $data['application']['env']['url']);
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();        
        $data['application'] = parse_ini_file($this->Environment->getConfig().'application.ini', true);
        $Matcher = $Factory->buildConfigExpressionMatcher();

        return [
            [$Matcher, $data]
        ];
    }

}
