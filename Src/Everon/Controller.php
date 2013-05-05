<?php
namespace Everon;


abstract class Controller implements Interfaces\Controller
{
    use Dependency\View;
    use Dependency\ModelManager;

    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Request;
    use Dependency\Injection\Router;

    use Helper\ToString;

    /**
     * Controller's name
     * @var string
     */
    protected $name = null;

    /**
     * @param Interfaces\View $View
     * @param Interfaces\ModelManager $ModelManager
     */
    public function  __construct(Interfaces\View $View, Interfaces\ModelManager $ModelManager)
    {
        $this->View = $View;
        $this->ModelManager = $ModelManager;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        if (is_null($this->name)) {
            $this->setName(get_class($this));
        }
        
        return $this->name;
    }

    /**
     * @return Interfaces\TemplateContainer
     */
    public function getOutput()
    {
        return $this->getView()->getOutput();
    }

    /**
     * @param mixed $Output
     */
    public function setOutput($Output)
    {
        $this->getView()->setOutput($Output);
    }

    public function getToString()
    {
        return (string) $this->getOutput();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModel($name)
    {
        return $this->getModelManager()->getModel($name);
    }

}