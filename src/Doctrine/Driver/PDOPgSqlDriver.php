<?php


namespace App\Doctrine\Driver;

use App\Doctrine\PostgreSQL119Platform;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as ParentDriver;

class PDOPgSqlDriver extends ParentDriver implements Driver
{
    public function createDatabasePlatformForVersion($version)
    {
        return new PostgreSQL119Platform();
    }
    
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new Postgre11SqlSchemaManager($conn);
    }
}