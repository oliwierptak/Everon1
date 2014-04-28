<?php

namespace Everon\Email\Dependency;


trait EmailManager {
    /**
     * @var \Everon\Email\Interfaces\Manager
     */
    protected $EmailManager = null;

    /**
     * @param \Everon\Email\Interfaces\Manager $EmailManager
     */
    public function setEmailManager($EmailManager)
    {
        $this->EmailManager = $EmailManager;
    }

    /**
     * @return \Everon\Email\Interfaces\Manager
     */
    public function getEmailManager()
    {
        return $this->EmailManager;
    }

} 