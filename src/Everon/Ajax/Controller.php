<?php
namespace Everon\Ajax;

use Everon\Http;

/**
 * @method \Everon\Http\Interfaces\Response getResponse()
 */
abstract class Controller extends Http\Controller implements Interfaces\Controller
{
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
     */
    protected function prepareResponse($action, $result)
    {
        $this->getResponse()->setDataValue('result', $this->json_result);
        $this->getResponse()->setDataValue('data', $this->json_data);
        $this->getResponse()->setDataValue('errors', $this->getRouter()->getRequestValidator()->getErrors());
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
}