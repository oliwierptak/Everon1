<?php
namespace Everon\Ajax;

use Everon\Http;

/**
 * @method \Everon\Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Http\Controller
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
     * @return bool|void
     */
    protected function prepareResponse($action, $result)
    {
        if ($this->getResponse()->getStatusCode() === null) {
            $this->getResponse()->setStatusCode(($result ? 200 : 400));
        }

        $this->getResponse()->setDataValue('result', $this->getJsonResult() ?: $result);
        $this->getResponse()->setDataValue('data', $this->getJsonData());
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
        $this->setJsonData(null);
        $this->setJsonResult(false);
        $this->addValidationError('exception', $Exception->getMessage());
        $this->prepareResponse('', false);

        $this->response();
    }
}