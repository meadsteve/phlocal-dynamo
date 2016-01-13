<?php

namespace MeadSteve\PhlocalDynamo\Tests;

use MeadSteve\PhlocalDynamo\LocalDynamo;

class FileCheckingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocalDynamo
     */
    private $dynamo;


    public function tearDown()
    {
        $this->dynamo->stop();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThrowsExceptionAsJarIsMissing()
    {
        $this->dynamo = new LocalDynamo(9014, __DIR__ . "/no-good-path");
        $this->dynamo->start();
        $client = $this->dynamo->getClient();
        $client->listTables();
    }
}