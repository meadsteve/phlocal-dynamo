<?php

namespace MeadSteve\PhlocalDynamo;

use Aws\DynamoDb\DynamoDbClient;
use Cocur\BackgroundProcess\BackgroundProcess;

class LocalDynamo
{
    private $port = "9025";
    private $jarLocation;

    /**
     * @var \Cocur\BackgroundProcess\BackgroundProcess
     */
    private $process;

    /**
     * LocalDynamo constructor.
     */
    public function __construct($port = null, $jarLocation = null)
    {
        if (!is_null($port)) {
            $this->port = $port;
        }

        if (is_null($jarLocation)) {
            $this->jarLocation = __DIR__ . "/dynamo/";
        } else {
            $this->jarLocation = $jarLocation;
        }
    }

    public function start()
    {
        $jarPath = $this->jarLocation . "DynamoDBLocal.jar";
        $libPath = $this->jarLocation . "DynamoDBLocal_lib";
        if (!file_exists($jarPath)) {
            $error = "dynamo jar file not found at {$this->jarLocation}. "
                . "Refer to readme for installation instructions";
            throw new \RuntimeException($error);
        }
        $javaCmd = "java -Djava.library.path={$libPath} -jar {$jarPath} -sharedDb -inMemory -port {$this->port}";
        $this->process = new BackgroundProcess($javaCmd);
        $this->process->run();
        if(! $this->process->isRunning()) {
            throw new \RuntimeException("Unable to start local dynamodb");
        }
        return $this;
    }

    public function stop()
    {
        if ($this->process && $this->process->isRunning()) {
            $this->process->stop();
        }
    }

    function __destruct()
    {
        $this->stop();
    }


    /**
     * @return DynamoDbClient
     */
    public function getClient()
    {
        return new DynamoDbClient([
            'region' => 'local',
            'version' => '2012-08-10',
            'endpoint' => "http://localhost:" . $this->port
        ]);
    }
}
