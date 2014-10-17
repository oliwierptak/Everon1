<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

use Everon\Domain\Exception;
use Everon\Domain\Interfaces;
use Everon\Helper;
use Everon\Interfaces\Collection;

class Mapper implements Interfaces\Mapper
{
    use Helper\ToArray;
    
    /**
     * @var Collection
     */
    protected $MappingCollection = null;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->MappingCollection = new Helper\Collection($data);
    }

    /**
     * @inheritdoc
     */
    public function getTableName($domain_name)
    {
        /**
         * @var \Everon\Config\Interfaces\ItemDomain $Item
         */
        $Item = $this->MappingCollection->get($domain_name, null);
        if ($Item !== null) {
            return $Item->getTable();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDomainName($data_mapper_name)
    {
        /**
         * @var \Everon\Config\Interfaces\ItemDomain $Item
         */
        $data = $this->MappingCollection->toArray();
        foreach ($data as $domain_name => $Item) {
            if (strcasecmp($data_mapper_name, $Item->getTable()) === 0) {
                return $domain_name;
            }
        }

        return null;
    }
    
    protected function getToArray()
    {
        return $this->MappingCollection->toArray();
    }
}
