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

use Everon\Helper;

class CurlAdapter implements Interfaces\CurlAdapter
{
    use Helper\Arrays;
    use Helper\Exceptions;

    /**
     * @var int
     */
    protected $http_response_code = null;
    
    /**
     * @param $url
     * @param array $options
     * @return resource
     */
    protected function getCurlProcess($url, array $options=[])
    {
        $defaults = [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], 
            CURLOPT_RETURNTRANSFER => 1, 
            CURLOPT_HEADER => 0, 
            CURLOPT_VERBOSE => 0, 
            CURLOPT_USERPWD => 'goldfinger@grofas.com' . ":" . 'easy', 
            CURLOPT_TIMEOUT => 5, 
        ];
        $options = $this->arrayMergeDefault($defaults, $options);

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_URL, $url);

        return $curl;
    }

    /**
     * @param $curl
     * @return mixed|null
     * @throws Exception\Curl
     */
    protected function execute($curl)
    {
        $response = null;
        try {
            $response = curl_exec($curl);
            $this->http_response_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($this->http_response_code !== 200 && $this->http_response_code !== 201 && $this->http_response_code !== 204) {
                $error = json_decode($response, true);
                $str = $error['data']['error'];
                throw new Exception\Resource($str);
            }
        }
        catch (\Exception $e) {
            throw new Exception\Curl($e);
        }
        finally {
            curl_close($curl);
            return $response;
        }
    }

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    public function put($url, $data)
    {
        $ch = $this->getCurlProcess($url, [
            CURLOPT_PUT => 1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data
        ]);
        return $this->execute($ch);
    }

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    public function post($url, $data)
    {
        $ch = $this->getCurlProcess($url, [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        return $this->execute($ch);
    }

    /**
     * @param $url
     * @return mixed|null
     */
    public function get($url)
    {
        $ch = $this->getCurlProcess($url);
        return $this->execute($ch);
    }

    /**
     * @param $url
     * @return mixed|null
     */
    public function delete($url)
    {
        $ch = $this->getCurlProcess($url, [
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER => 0,
            CURLOPT_CUSTOMREQUEST => 'DELETE'
        ]);
        return $this->execute($ch);
    }

    /**
     * @param $url
     * @return mixed|null
     */
    public function head($url)
    {
        $ch = $this->getCurlProcess($url, [
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER => 0,
            CURLOPT_CUSTOMREQUEST => 'HEAD'
        ]);
        return $this->execute($ch, true);
    }

    /**
     * @param int $http_response_code
     */
    public function setHttpResponseCode($http_response_code)
    {
        $this->http_response_code = $http_response_code;
    }

    /**
     * @return int
     */
    public function getHttpResponseCode()
    {
        return $this->http_response_code;
    }
    
}