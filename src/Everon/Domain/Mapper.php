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
    public function getDataMapperNameByDomain($domain_name)
    {
        $data = $this->MappingCollection->toArray();
        foreach ($data as $data_mapper_name => $Item) {
            if ($domain_name === $Item->getDomain()) {
                return $data_mapper_name;
            }
        }
        
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDomainNameByDataMapper($data_mapper_name)
    {
        $Item = $this->MappingCollection->get($data_mapper_name, null);
        if ($Item !== null) {
            return $Item->getDomain();
        }
        
        return null;
    }

    /**
     * @param $domain_name
     * @return \Everon\Config\Interfaces\ItemDomain|null
     */
    public function getByDomainName($domain_name)
    {
        /**
         * @var \Everon\Config\Interfaces\ItemDomain $Item
         */
        $data = $this->MappingCollection->toArray();
        foreach ($data as $data_mapper_name => $Item) {
            if ($domain_name === $Item->getDomain()) {
                return $Item;
            }
        }

        return null;
    }

    /**
     * @param $data_mapper_name
     * @return \Everon\Config\Interfaces\ItemDomain|null
     */
    public function getByDataMapperName($data_mapper_name)
    {
        return $this->MappingCollection->get($data_mapper_name, null);
    }
    
    protected function getToArray()
    {
        return $this->MappingCollection->toArray();
    }
}
