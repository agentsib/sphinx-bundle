<?php
/**
 * User: ikovalenko
 */

namespace AgentSIB\SphinxBundle\Model;


class SphinxLogger
{
    private $queries = array();

    public function logQuery($query, $duration, $connection, $error = false)
    {
        $this->queries[] = array(
            'query'   =>  $query,
            'duration' => $duration,
            'connection' => $connection,
            'error' =>  $error
        );
    }

    public function getQueries()
    {
        return $this->queries;
    }

}