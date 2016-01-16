<?php

namespace MeadSteve\PhlocalDynamo;

use Aws\DynamoDb\DynamoDbClient;
use Cocur\BackgroundProcess\BackgroundProcess;

class LocalDynamo
{
    private $port = "9025";
    private $jarLocation;
    private $jarPath;
    private $libPath;

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
        $this->libPath = $this->jarLocation . "DynamoDBLocal_lib";
        $this->jarPath = $this->jarLocation . "DynamoDBLocal.jar";
    }

    public function start()
    {
        $this->ensureDynamoJarExists();
        $this->startDynamoJar();
        return $this;
    }

    public function stop()
    {
        if ($this->process && $this->process->isRunning()) {
            $this->process->stop();
        }
    }

    /**
     * @return DynamoDbClient
     */
    public function getClient()
    {
        return new DynamoDbClient([
            'credentials' => array(
                'key'    => 'YOUR_AWS_ACCESS_KEY_ID',
                'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY',
            ),
            'region' => 'local',
            'version' => '2012-08-10',
            'endpoint' => "http://localhost:" . $this->port
        ]);
    }


    function __destruct()
    {
        $this->stop();
    }

    private function ensureDynamoJarExists()
    {
        if (!file_exists($this->jarPath)) {
            $error = "dynamo jar file not found at {$this->jarLocation}. "
                . "Refer to readme for installation instructions";
            throw new \RuntimeException($error);
        }
    }


    private function startDynamoJar()
    {
        $javaCmd = "java -Djava.library.path={$this->libPath} "
            . "-jar {$this->jarPath} -sharedDb -inMemory -port {$this->port}";
        $this->process = new BackgroundProcess($javaCmd);
        $this->process->run();
        if (!$this->process->isRunning()) {
            throw new \RuntimeException("Unable to start local dynamodb");
        }
    }
}
