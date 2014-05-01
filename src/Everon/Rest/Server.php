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
 * @method \Everon\Rest\Interfaces\Request getRequest
 */
class Server extends \Everon\Core implements Rest\Interfaces\Server
{
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
                echo $this->getResponse()->toJson(); //xxx
            }
            else {
                parent::run($RequestIdentifier);
            }
        }
        catch (Exception\Pdo $Exception) {
            $PdoException = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($PdoException, $this->Controller);
        }
        catch (Exception\RouteNotDefined $Exception) {
            $RouteNotDefined = new Http\Exception((new Http\Message\NotFound('Invalid resource name, request method or version')));
            $this->showException($RouteNotDefined, $this->Controller);
        }
        catch (Exception\InvalidRoute $Exception) {
            $BadRequest = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($BadRequest, $this->Controller);
        }
        catch (Http\Exception $Exception) {
            $this->showException($Exception, $this->Controller);
        }
        catch (Rest\Exception\Resource $Exception) {
            $RestException = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($RestException, $this->Controller);
        }
        catch (\Exception $Exception) {
            $InternalServerError = new Http\Exception((new Http\Message\InternalServerError($Exception->getMessage())));
            $this->showException($InternalServerError, $this->Controller);
        }
        finally {
            $url = $this->getConfigManager()->getConfigValue('rest.server.url');
            $this->getLogger()->rest(
                sprintf(
                    '[%d] %s %s (%s)',
                    $this->getResponse()->getStatusCode(),
                    $this->getRequest()->getMethod(), 
                    $url.$this->getRequest()->getFullPath(), 
                    $this->getResponse()->getStatusMessage()
                )
            );
        }
    }
    
 
    public function showException(Http\Exception $Exception, $Controller)
    {
        $message = $Exception->getMessage();
        $code = $Exception->getCode();
        if ($Exception instanceof Http\Exception) {
            $message = $Exception->getHttpMessage()->getInfo();
            $code = $Exception->getHttpMessage()->getCode();
        }

        $this->getResponse()->setData(['error' => $message]); //xxx
        
        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        echo $this->getResponse()->toJson();
    }

    public function shutdown()
    {
    }
}
