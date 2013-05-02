<?php
namespace Everon;


abstract class Controller implements Interfaces\Controller
{
    use Dependency\View;

    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Response;
    use Dependency\Injection\Request;
    use Dependency\Injection\Router;

    use Helper\ToString;


    /**
     * Controller's name
     * @var string
     */
    protected $name = null;

    /**
     * List of Models
     * @var array
     */
    protected $models = null;

    abstract function initModel();


    /**
     * @param Interfaces\View $View
     */
    public function  __construct(Interfaces\View $View)
    {
        $this->View = $View;
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
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildModel($name);
        }

        return $this->models[$name];
    }

    /**
     * @return array|null
     */
    public function getAllModels()
    {
        if (is_null($this->models)) {
            $this->initModel();
        }
        
        return $this->models;
    }

}