<?php
/**
 * User: ikovalenko
 */

namespace AgentSIB\SphinxBundle\DataCollector;


use AgentSIB\SphinxBundle\Model\SphinxLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SphinxDataCollector extends DataCollector
{

    private $logger;

    public function __construct(SphinxLogger $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['queries_count'] = count($this->logger->getQueries());
    }

    public function getQueriesCount()
    {
        return $this->data['queries_count'];
    }

    public function getName()
    {
        return 'agentsib_sphinx';
    }

}