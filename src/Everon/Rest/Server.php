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
 * @method \Everon\Rest\Interfaces\Request getRequest
 * @property Interfaces\Controller $Controller
 */
class Server extends \Everon\Http\Core implements Rest\Interfaces\Server
{
    /**
     * @inheritdoc
     */
    public function run(RequestIdentifier $RequestIdentifier)
    {
        try {
            $response_headers = $this->getConfigManager()->getConfigValue('rest.response_headers', []);
            foreach ($response_headers as $name => $values) {
                $origin = $this->getRequest()->getHeader('HTTP_ORIGIN', null);
                if (strcasecmp($values['Access-Control-Allow-Origin'], $origin) === 0) {
                    foreach ($values as $header_name => $header_value) {
                        $this->getResponse()->setHeader($header_name, $header_value);
                    }
                }
            }
            
            if ($this->getRequest()->getMethod() === \Everon\Request::METHOD_OPTIONS) {
                $OkNoContent = new Http\Message\OkNoContent();//DRY
                $this->getResponse()->setStatusCode($OkNoContent->getCode());
                $this->getResponse()->setStatusMessage($OkNoContent->getMessage());
                $this->getResponse()->toJson(); //xxx
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
    }
 
    public function showException(Http\Exception $Exception, $Controller)
    {
        $this->getLogger()->error($Exception);
        
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
}
