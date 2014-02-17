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
use Everon\Exception;

class Request extends \Everon\Request implements Interfaces\Request
{
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    /**
     * @param array $server $_SERVER
     * @param array $get $_GET
     * @param array $post $_POST
     * @param array $files $_FILES
     */
    public function __construct22(array $server, array $get, array $post, array $files)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        sd($data, $_POST, $_SERVER);
        
        parent::__construct($server, $get, $post, $files);
        $this->initRequest();

    }
    
}
