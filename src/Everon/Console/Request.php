<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Console;

use Everon\Interfaces;
use Everon\Helper;

class Request extends \Everon\Request
{
    protected function initRequest()
    {
        $this->overwriteEnvironment();
        parent::initRequest();
    }

    /**
     * @return array
     */
    protected function overwriteEnvironment()
    {
        $args = $this->ServerCollection['argv'];
        array_shift($args);
        $uri_tokens = array_shift($args);
        $query_string = implode('&', $args);
        $method = 'GET';
        $uri = '/'.$uri_tokens;
        
        if ($query_string !== '') {
            $uri .= '?'.$query_string;
        }
        
        $this->ServerCollection['REQUEST_METHOD'] = $method;
        $this->ServerCollection['REQUEST_URI'] = $uri;
        $this->ServerCollection['QUERY_STRING'] = $query_string;
        $this->ServerCollection['SERVER_NAME'] = $this->ServerCollection['SCRIPT_FILENAME'];

        //[QUERY_STRING] => help&id=12&foo=bar
        //[REQUEST_URI] => /?help&id=12&foo=bar
        parse_str($query_string, $get);

        $current_get = $this->getGetCollection()->toArray();
        
        $this->setGetCollection(array_merge(
            $current_get, $get
        ));
    }
}