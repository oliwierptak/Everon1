<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper;

interface EntityInterface
{
    function isNew();
    function isModified();
    function isPersisted();
    function isDeleted();
    function getId();
    function getValueByName($name);
    function incept();
    function modify();
    function persist();
}
