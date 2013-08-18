<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Core\Console;

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
    public function overwriteEnvironment()
    {
        $args = $this->ServerCollection['argv'];
        array_shift($args);
        $uri =  implode('&', $args);
        array_shift($args);

        $method = 'GET';
        $query_string = implode('&', $args);
        
        $this->ServerCollection['REQUEST_METHOD'] = $method;
        $this->ServerCollection['REQUEST_URI'] = '/?'.$uri;
        $this->ServerCollection['QUERY_STRING'] = $query_string;
        $this->ServerCollection['SERVER_NAME'] = $this->ServerCollection['SCRIPT_FILENAME'];


        //[QUERY_STRING] => help&me=12&bla=ola
        //[REQUEST_URI] => /?help&me=12&bla=ola
        parse_str($query_string, $get);

        $current_get = $this->getQueryCollection();
        
        //set get
        $this->setQueryCollection(array_merge(
            $current_get, $get
        ));
    }
}