<?php
/**
 * User: ikovalenko
 */

namespace AgentSIB\SphinxBundle\DataCollector;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SphinxDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

    }



    public function getName()
    {
        return 'agentsib_sphinx';
    }

}