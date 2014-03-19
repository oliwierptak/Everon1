<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces;

interface ItemDomain extends Item
{
    /**
     * @return array
     */
    function getConnections();

    /**
     * @param array $connections
     */
    function setConnections($connections);

    /**
     * @return string
     */
    function getDomain();
    
    /**
     * @param string $domain
     */
    function setDomain($domain);
    
    /**
     * @return string
     */
    function getIdField();

    /**
     * @param string $id_field
     */
    function setIdField($id_field);
}