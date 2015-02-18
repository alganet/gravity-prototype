<?php

namespace Supercluster\Gravity\Datasources;

use Respect\Relational\Sql as RespectSql;

/**
 * The SQL dialect with extra statements
 */
class Sql extends RespectSql
{
    protected function build($operation, $parts)
    {
        switch ($operation) {
            case 'createTableIfNotExists':
                $this->buildFirstPart($parts);
                return $this->buildParts($parts, '(%s) ');
            default:
                return parent::build($operation, $parts);
        }
    }
}
