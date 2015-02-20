<?php

namespace Supercluster\Gravity\Datasources;

use Respect\Relational\Sql as RespectSql;

/**
 * The SQL dialect with extra statements
 */
class Sql extends RespectSql
{
    /**
     * Method used to translate from php method calls to SQL instructions.
     * It is closely related to __call for the Respect\Relational\Sql class.
     */
    protected function build($operation, $parts)
    {
        switch ($operation) {
            // Allows Sql::createTableIfNotExists($columns)
            case 'createTableIfNotExists':
                $this->buildFirstPart($parts);
                return $this->buildParts($parts, '(%s) ');
            default:
                return parent::build($operation, $parts);
        }
    }
}
