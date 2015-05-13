<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Ajax;

use Everon\Helper;
use Everon\Exception;
use Everon\Http;
use Everon\Interfaces;

class Request extends Http\Request implements Interfaces\Request
{
    /**
     * @inheritdoc
     */
    public function getRawInput()
    {
        $result = null;
        $input = parent::getRawInput();

        $input = trim($input);
        if ($input !== '') {
            $result = json_decode($input, true);
        }

        if (is_array($result) === false) {
            $result = [];
        }

        return $result;
    }
}
