<?php
namespace Everon\Ajax;

use Everon\Http;

/**
 * @method \Everon\Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    use Http\Dependency\Injection\HttpSession;

    /**
     * @var array
     */
    protected $json_data = null;

    /**
     * @var mixed
     */
    protected $json_result = null;


    /**
     * @param $action
     * @param $result
     * @return bool|void
     */
    protected function prepareResponse($action, $result)
    {
        $this->getResponse()->setStatusCode(($result ? 200 : 400));

        if ($this->getRouter()->getRequestValidator()->hasFatalError()) {
            $this->getResponse()->setDataValue('result', false);
            $this->getResponse()->setDataValue('error', $this->getRouter()->getRequestValidator()->getFatalError());
        }
        else {
            $this->getResponse()->setDataValue('result', $this->json_result);
            $this->getResponse()->setDataValue('data', $this->json_data);
            $this->getResponse()->setDataValue('errors', $this->getRouter()->getRequestValidator()->getErrors());
        }
    }

    protected function response()
    {
        echo $this->getResponse()->toJson();
    }

    /**
     * @inheritdoc
     */
    public function setJsonData($json_data)
    {
        $this->json_data = $json_data;
    }

    /**
     * @inheritdoc
     */
    public function getJsonData()
    {
        return $this->json_data;
    }

    /**
     * @inheritdoc
     */
    public function getJsonResult()
    {
        return $this->json_result;
    }

    /**
     * @inheritdoc
     */
    public function setJsonResult($json_result)
    {
        $this->json_result = (bool) $json_result;
    }

    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception)
    {

        $this->getResponse()->setDataValue('result', false);
        $this->getResponse()->setDataValue('error', $Exception->getMessage());

        $this->response();
    }

    /**
     * @param $flash_message
     */
    public function setFlashMessage($flash_message)
    {
        $this->getHttpSession()->setFlashMessage($flash_message);
    }

    /**
     * @return string
     */
    public function getFlashMessage()
    {
        return $this->getHttpSession()->getFlashMessage();
    }

    public function resetFlashMessage()
    {
        $this->getHttpSession()->resetFlashMessage();
    }
}