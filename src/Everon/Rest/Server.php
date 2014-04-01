<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Dependency;
use Everon\Interfaces;
use Everon\Exception;
use Everon\RequestIdentifier;
use Everon\Http;
use Everon\Rest;

/**
 * @method \Everon\Http\Interfaces\Response getResponse
 */
class Server extends \Everon\Core implements Rest\Interfaces\Server
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Response;
    
    /**
     * @var Rest\Interfaces\Controller
     */
    protected $Controller = null;


    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            $response_headers = $this->getConfigManager()->getConfigValue('rest.response_headers', null);
            if ($response_headers !== null) {
                foreach ($response_headers as $name => $values) {
                    $origin = $this->getRequest()->getHeader('HTTP_ORIGIN', null);
                    if (strcasecmp($values['Access-Control-Allow-Origin'], $origin) === 0) {
                        foreach ($values as $header_name => $header_value) {
                            $this->getResponse()->setHeader($header_name, $header_value);
                        }
                    }
                }
            }
            
            if ($this->getRequest()->getMethod() === \Everon\Request::METHOD_OPTIONS) {
                $this->getLogger()->response('[%s] %s : %s', [$this->getResponse()->getStatusCode(), $this->getRequest()->getPath(), $this->getRequest()->getMethod()]);
                echo $this->getResponse()->toJson(); //xxx
            }
            else  {
                parent::run($RequestIdentifier);
            }
        }
        catch (Exception\RouteNotDefined $Exception) {
            $this->getLogger()->error($Exception);
            $NotFound = new Http\Exception\NotFound('Resource not found: '.$Exception->getMessage());
            $this->showException($NotFound->getHttpStatus(), $NotFound);
        }
        catch (Rest\Exception\Resource $Exception) {
            $this->showException(404, $Exception);
        }
        catch (\Exception $Exception) {
            $this->getLogger()->error($Exception);
            $this->showException(500, $Exception);
        }
    }
    
    /**
     * @param $code
     * @param \Exception $Exception
     */
    public function showException($code, \Exception $Exception)
    {
        $message = $Exception->getMessage();
        $this->getResponse()->setData(['error' => $message]);
        if ($Exception instanceof Http\Exception) {
            $message = $Exception->getHttpMessage();
            $code = $Exception->getHttpStatus();
        }

        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        echo $this->getResponse()->toJson();
    }

    public function shutdown()
    {
    }
}
