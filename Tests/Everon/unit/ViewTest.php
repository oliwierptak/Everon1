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

class ViewTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $IndexTemplateMock = $this->getMock('Everon\Interfaces\Template', [], [], '', false);
        $View = new \Everon\Test\MyView($this->getTemplateDirectory(), [], $IndexTemplateMock, '.htm');
        $this->assertInstanceOf('Everon\View', $View);
    }

    public function setupTemplateFile($filename, $content)
    {
        file_put_contents($filename, $content);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetGet(\Everon\Interfaces\View $View)
    {
        $View->set('test', 'me');
        $this->assertEquals('me', $View->get('test'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetContainerShouldCopyAssignedDataFromViewToContainerTemplate(\Everon\Interfaces\View $View)
    {
        $this->markTestSkipped();
        $Form = new \Everon\View\Html\Form([
            'action' => '/login/submit'
        ]);

        $FakeUser = new \Everon\Helper\Popo(['username'=>'test']);
        $View->set('User', $FakeUser);
        $View->set('Form', $Form);

        /**
         * @var \Everon\Interfaces\TemplateContainer $Template
         */
        $Template = $View->getTemplate('ViewTest_form', ['Test'=>'I was Included']);
        $View->setContainer($Template);

        $expected =
<<<EOF
I was Included
<form action="/login/submit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="token" value="3">
    <table border="0">
    <tr>
        <td>Username</td>
        <td>:</td>
        <td><input type="text" value="test" name="username"></td>
    </tr>
    <tr>
        <td>Password</td>
        <td>:</td>
        <td><input type="password" name="password" /></td>
    </tr>
    <tr>
        <td colspan="3"><input type="submit"></td>
    </tr>
    </table>
</form>
EOF;
        $actual = (string) $View->getContainer();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testContainerTemplate(\Everon\Interfaces\View $View)
    {
        $this->markTestSkipped();
        $Form = new \Everon\View\Html\Form([
            'action' => '/login/submit'
        ]);

        $expected =
<<<EOF
<b>Included test</b>
<h1>I was included</h1>
<form action="/login/submit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="token" value="3">
    <table border="0">
    <tr>
        <td>Username</td>
        <td>:</td>
        <td><input type="text" value="test" name="username"></td>
    </tr>
    <tr>
        <td>Password</td>
        <td>:</td>
        <td><input type="password" name="password" /></td>
    </tr>
    <tr>
        <td colspan="3"><input type="submit"></td>
    </tr>
    </table>
</form>
EOF;

        $FakeUser = new \Everon\Helper\Popo(['username'=>'test']);

        /**
         * @var \Everon\Interfaces\TemplateContainer $Template
         */
        $Template = $View->getTemplate('ViewTest_form', [
            'Form' => $Form,
            'User' => $FakeUser
        ]);

        $Include = $View->getTemplate('ViewTest_include', [
            'name' => 'test',
        ]);

        $Include2 = $View->getTemplate('ViewTest_include_2', [
            'text' => 'I was included',
        ]);

        $Include->set('IncludeMeNow', $Include2);
        $Template->set('Test', $Include);

        $View->setContainer($Template);
        $Output = $View->getContainer();

        $actual = (string) $Output;

        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $Output);
        $this->assertEquals($expected, $actual);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $Template);
        $this->assertEquals($this->getTemplateDirectory().'ViewTest_form.htm', $Template->getTemplateFile());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetConatiner(\Everon\Interfaces\View $View)
    {
        $View->setContainer('');
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());

        $View->setContainer([]);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
        
        $View->setContainer(new \Everon\View\Template\Container('', []));
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Template
     * @expectedExceptionMessage Invalid Container type
     */
    public function testSetContainerShouldThrowExceptionWhenWrongInputIsSet(\Everon\Interfaces\View $View)
    {
        $View->setContainer(null);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $View->getContainer());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetContainerShouldReturnIndexContainerWhenNull(\Everon\Interfaces\View $View)
    {
        $PropertyContainer = $this->getProtectedProperty('Everon\View', 'Container');
        $PropertyContainer->setValue($View, null);
        
        $Output = $View->getContainer();
        $this->assertEquals(file_get_contents($this->getTemplateDirectory().'Index/index.htm'), (string) $Output);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Template = $Factory->buildTemplate($this->getTemplateDirectory().'Index/index.htm', []);
        
        $IndexViewMock = $this->getMock('Everon\View', [], [], '', false);
        $IndexViewMock->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($Template));
        
        $ViewManagerMock = $this->getMock('Everon\View\Manager', [], [], '', false);
        $ViewManagerMock->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($IndexViewMock));
        
        $View = $Factory->buildView('MyView', $this->getTemplateDirectory(), [], $Template, '.htm', 'Everon\Test');
        
        return [
            [$View]
        ];
    }

}
