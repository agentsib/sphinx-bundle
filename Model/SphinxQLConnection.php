<?php
/**
 * User: ikovalenko
 */

namespace AgentSIB\SphinxBundle\Model;

use Foolz\SphinxQL\Drivers\ConnectionInterface as SphinxQLConnectionInterface;
use Foolz\SphinxQL\Drivers\Mysqli\Connection as MysqliSphinxConnection;
use Foolz\SphinxQL\Drivers\Pdo\Connection as PdoSphinxConnection;
use Foolz\SphinxQL\SphinxQL;

class SphinxQLConnection implements SphinxQLConnectionInterface
{
    const DRIVER_PDO = 'pdo';
    const DRIVER_MYSQLI = 'mysqli';

    private $alias;

    private $connection;

    /** @var  SphinxLogger */
    private $logger;

    public static function getSupportedDrivers()
    {
        return array(self::DRIVER_PDO, self::DRIVER_MYSQLI);
    }

    public function __construct($alias, $driver, $params = array())
    {
        $this->alias = $alias;

        switch($driver) {
            case self::DRIVER_PDO:
                $this->connection = new PdoSphinxConnection();
                break;
            case self::DRIVER_MYSQLI:
                $this->connection = new MysqliSphinxConnection();
                break;
            default:
                throw new \Exception('Unknown driver');
        }
        $this->connection->setParams($params);
    }

    public function addLogger(SphinxLogger $logger)
    {
        $this->logger = $logger;
    }

    public function createQueryBuilder()
    {
        return SphinxQL::create($this);
    }

    public function query($query)
    {
        $result = null;
        $exception = null;
        try {
            $result = $this->connection->query($query);
        } catch (\Exception $e) {
            $exception = $e;
        }

        if ($this->logger) {
            $this->logger->logQuery($query, 0, $this->alias, $exception?$exception->getMessage():false);
        }

        if ($exception) {
            throw $exception;
        }

        return $result;
    }

    public function multiQuery(Array $queue)
    {
        return $this->connection->multiQuery($queue);
    }

    public function escape($value)
    {
        return $this->connection->escape($value);
    }

    public function quoteIdentifier($value)
    {
        return $this->connection->quoteIdentifier($value);
    }

    public function quoteIdentifierArr(Array $array = array())
    {
        return $this->connection->quoteIdentifierArr($array);
    }

    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    public function quoteArr(Array $array = array())
    {
        return $this->connection->quoteArr($array);
    }

}