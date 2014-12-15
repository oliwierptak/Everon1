<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\Column\Validator\PostgreSql;

use Everon\DataMapper\Interfaces;
use Everon\DataMapper\Schema\Column\AbstractValidator;

class String extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validateValue($value)
    {
        return is_string($value);
    }
}