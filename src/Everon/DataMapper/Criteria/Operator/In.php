<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Criteria\Operator;

use Everon\DataMapper\Interfaces;

class In extends \Everon\DataMapper\Criteria\Operator implements Interfaces\Criteria\Operator
{
    protected $type = self::TYPE_IN;

    public function getTypeAsSql()
    {
        return 'IN';
    }

    /**
     * @inheritdoc
     */
    public function toSqlPartData(Interfaces\Criteria\Criterium $Criterium)
    {
        $params = [];
        
        foreach ($Criterium->getValue() as $value) {
            $rand = $Criterium->getColumn().'_'.rand(100, time());
            $params[$rand] = $value;
        }

        $placeholder_sql = ':'.rtrim(implode(',:', array_keys($params)), ',');
        $sql = sprintf("%s %s (%s)", $Criterium->getColumn(), $this->getTypeAsSql(), $placeholder_sql);
        return [$sql, $params];
    }
}