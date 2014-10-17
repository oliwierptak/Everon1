<?php
namespace Everon\Ajax;

use Everon\Http;

/**
 * @method \Everon\Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Controller implements Interfaces\Controller
{
    /**
     * @var array
     */
    protected $json_data = null;
    
    protected $json_result = null;


    /**
     * @param $action
     * @param $result
     */
    protected function prepareResponse($action, $result)
    {
        $this->getResponse()->setData([
            'result' => ($this->json_result !== null) ? $this->json_result : $result,
            'data' => $this->json_data
        ]);
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
}