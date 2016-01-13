<?php


namespace MeadSteve\PhlocalDynamo;


use Aws\DynamoDb\DynamoDbClient;
use Cocur\BackgroundProcess\BackgroundProcess;

class LocalDynamo
{
    private $port = "9025";

    /**
     * @var \Cocur\BackgroundProcess\BackgroundProcess
     */
    private $process;

    /**
     * LocalDynamo constructor.
     */
    public function __construct()
    {
    }

    public function start()
    {
        $jarPath = __DIR__ . "/dynamo/DynamoDBLocal.jar";
        $libPath = __DIR__ . "/dynamo/DynamoDBLocal_lib";
        $javaCmd = "java -Djava.library.path={$libPath} -jar {$jarPath} -sharedDb -inMemory -port {$this->port}";
        $this->process = new BackgroundProcess($javaCmd);
        $this->process->run();
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
