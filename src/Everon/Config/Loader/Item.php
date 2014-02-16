<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Loader;

use Everon\Config\Interfaces;
use Everon\Helper;

class Item implements Interfaces\LoaderItem
{
    use Helper\ToArray;
    
    protected $filename = null;
    
    
    public function __construct($filename, array $data)
    {
        $this->filename = $filename;
        $this->data = $data;
    }
    
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}   