<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;


trait ToString
{

    protected $to_string = '';

    public function __toString()
    {
        try
        {
            if ($this->to_string === '' && method_exists($this, 'getToString')) {
                $this->to_string = $this->getToString();
            }

            return $this->to_string;
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

}
