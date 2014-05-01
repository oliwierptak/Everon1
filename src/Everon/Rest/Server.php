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
            $BadRequest = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($BadRequest->getHttpMessage()->getCode(), $BadRequest);
        }
        catch (Exception\RouteNotDefined $Exception) {
            $BadRequest = new Http\Exception((new Http\Message\NotFound('Invalid resource name, request method or version')));
            $this->showException($BadRequest->getHttpMessage()->getCode(), $BadRequest);
        }
        catch (Exception\InvalidRoute $Exception) {
            $BadRequest = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($BadRequest->getHttpMessage()->getCode(), $BadRequest);
        }
        catch (Http\Exception $Exception) {
            $this->showException($Exception->getHttpMessage()->getCode(), $Exception);
        }
        catch (Rest\Exception\Resource $Exception) {
            $BadRequest = new Http\Exception((new Http\Message\BadRequest($Exception->getMessage())));
            $this->showException($BadRequest->getHttpMessage()->getCode(), $BadRequest);
        }
        catch (\Exception $Exception) {
            $InternalServerError = new Http\Exception((new Http\Message\InternalServerError($Exception->getMessage())));
            $this->showException($InternalServerError->getHttpMessage()->getCode(), $InternalServerError);
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
    
/*    
    public function showException(Http\Exception $Exception) //dry
    {
        $message = $Exception->getMessage();
            //$message = $Exception->getHttpMessage()->getMessage();
            $message = $Exception->getHttpMessage()->getInfo() !== '' ? $Exception->getHttpMessage()->getInfo() :  $Exception->getHttpMessage()->getMessage();
            $code = $Exception->getHttpMessage()->getCode();

        $this->getResponse()->setData(['error' => $message]); //xxx
        
        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setStatusMessage($message);
        echo $this->getResponse()->toJson();
    }*/

    public function shutdown()
    {
    }
}
