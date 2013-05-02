<?php
namespace Everon\Dependency;


trait View
{

    protected $View = null;


    /**
     * @return \Everon\Interfaces\View
     */
    public function getView()
    {
        return $this->View;
    }

    /**
     * @param \Everon\Interfaces\View $View
     */
    public function setView(\Everon\Interfaces\View $View)
    {
        $this->View = $View;
    }

}
