<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\Dependency;
use Everon\Http;


/**
 * @method Http\Interfaces\Response getResponse()
 */
abstract class Controller extends \Everon\Controller implements Http\Interfaces\Controller
{
    use Http\Dependency\Injection\HttpSession;

    /**
     * @param $flash_message
     */
    public function setFlashMessage($flash_message)
    {
        $this->getHttpSession()->setFlashMessage($flash_message);
    }

    /**
     * @return string
     */
    public function getFlashMessage()
    {
        return $this->getHttpSession()->getFlashMessage();
    }

    public function resetFlashMessage()
    {
        $this->getHttpSession()->resetFlashMessage();
    }
}