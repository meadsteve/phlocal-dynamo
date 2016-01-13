<?php

namespace MeadSteve\PhlocalDynamo\Tests;

use Aws\DynamoDb\DynamoDbClient;
use MeadSteve\PhlocalDynamo\LocalDynamo;

class LocalDynamoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocalDynamo
     */
    private $dynamo;

    public function setUp()
    {
        parent::setUp();
        $this->dynamo = new LocalDynamo();
        $this->dynamo->start();
    }

    public function tearDown()
    {
        $this->dynamo->stop();
    }

    public function testReturnsClient()
    {
        $client = $this->dynamo->getClient();
        $this->assertInstanceOf(DynamoDbClient::class, $client);
    }

    public function testBasicTableCreate()
    {
        $client = $this->dynamo->getClient();
        $client->createTable($this->testTableConfig);
        $client->waitUntil('TableExists', array(
            'TableName' => 'test-table-mead-steve'
        ));
        $result = $client->listTables();
        $this->assertEquals(['test-table-mead-steve'], $result->get('TableNames'));
    }

    private $testTableConfig = [
        'TableName' => 'test-table-mead-steve',
        'AttributeDefinitions' => array(
            array(
                'AttributeName' => 'id',
                'AttributeType' => 'N'
            ),
            array(
                'AttributeName' => 'time',
                'AttributeType' => 'N'
            )
        ),
        'KeySchema' => array(
            array(
                'AttributeName' => 'id',
                'KeyType'       => 'HASH'
            ),
            array(
                'AttributeName' => 'time',
                'KeyType'       => 'RANGE'
            )
        ),
        'ProvisionedThroughput' => array(
            'ReadCapacityUnits'  => 10,
            'WriteCapacityUnits' => 20
        )
    ];
}