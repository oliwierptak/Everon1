<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\RequestIdentifier;
use Everon\Exception;
use Everon\Helper;
use Everon\Http;

/**
 * @method \Everon\Http\Interfaces\Response getResponse
 * @method \Everon\Http\Interfaces\Controller getController
 */
class Core extends \Everon\Core implements \Everon\Interfaces\Core
{
    /**
     * @var \Everon\Interfaces\Controller
     */
    protected $Controller = null;

    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            parent::run($RequestIdentifier);
        }
        catch (Http\Exception $Exception) {
            $this->showException($Exception, $this->Controller);
        }
    }

    /**
     * @param \Exception $Exception
     * @param \Everon\Mvc\Interfaces\Controller|null $Controller
     */
    protected function showException(\Exception $Exception, $Controller)
    {
        $message = '';
        $code = $Exception->getCode();
        if ($Exception instanceof Http\Exception) {
            $message = $Exception->getHttpMessage()->getMessage();
            $code = $Exception->getHttpMessage()->getCode();
        }

        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        
        parent::showException($Exception, $Controller);
    }

    public function shutdown()
    {
        $s = parent::shutdown();

        $this->getLogger()->log('response', 
            sprintf(
                '[%s] (%d) %s : %s %s',
                $s,
                $this->getResponse()->getStatusCode(),
                $this->getResponse()->getStatusMessage(),
                $this->getRequest()->getMethod(),
                $this->getRequest()->getPath()
            )
        );

        return $s;
    }

    /**
     * @inheritdoc
     */
    public function redirectAndTerminate($name, $query=[], $get=[])
    {
        $url = $this->getUrl($name, $query, $get);
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setDataValue('redirect', $url);
            $this->getResponse()->send();
            echo $this->getResponse()->toJson();
        }
        else {
            $this->getResponse()->setHeader('location', $url);
            $this->getResponse()->send();
        }
        
        $this->shutdown();
        $this->terminate();
    }
}
