<?php


namespace App\Doctrine\Driver;

use Doctrine\DBAL\Schema\PostgreSqlSchemaManager;
use Doctrine\DBAL\Schema\Sequence;

class Postgre11SqlSchemaManager extends PostgreSqlSchemaManager
{
    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence)
    {
        if ($sequence['schemaname'] !== 'public') {
            $sequenceName = $sequence['schemaname'] . "." . $sequence['relname'];
        } else {
            $sequenceName = $sequence['relname'];
        }
        if ( ! isset($sequence['increment_by'], $sequence['min_value'])) {
            $data      = $this->_conn->fetchAssoc('SELECT min_value, increment_by FROM ' . $this->_platform->quoteIdentifier($sequenceName));
            $sequence += $data;
        }
        return new Sequence($sequenceName, $sequence['increment_by'], $sequence['min_value']);
    }
}