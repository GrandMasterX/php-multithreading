<?php

namespace php\multithreading;

/**
 * Class Threads
 * @package php\multithreading
 */
class Threads {

    /**
     * @var string
     */
    public $phpPath = 'php';

    /**
     * @var int
     */
    private $lastId = 0;

    /**
     * @var array
     */
    private $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w']
    ];

    /**
     * @var array
     */
    private $handles = [];
    /**
     * @var array
     */
    private $streams = [];
    /**
     * @var array
     */
    private $results = [];
    /**
     * @var array
     */
    private $pipes = [];
    /**
     * @var int
     */
    private $timeout = 10;

    /**
     * @param $filename
     * @param array $params
     * @return int
     */
    public function newThread($filename, $params=[]) {

        $filename_or=$filename;
        $filename=explode(" ",$filename);
        $filename=$filename[0];
        if (!file_exists($filename)) {
            echo ('FILE_NOT_FOUND');die;
        }

        $filename=$filename_or;

        $params = addcslashes(serialize($params), '"');
        $command = $this->phpPath.' -q '.$filename.' --params "'.$params.'"';

        ++$this->lastId;

        $this->handles[$this->lastId] = proc_open($command, $this->descriptorSpec, $pipes);
        $this->streams[$this->lastId] = $pipes[1];
        $this->pipes[$this->lastId] = $pipes;

        return $this->lastId;
    }

    /**
     * @return bool|string
     */
    public function iteration() {
        if (!count($this->streams)) {
            return false;
        }
        $read = $this->streams;
        $write = null;
        $except= null;
        stream_select($read, $write, $except, $this->timeout);
        /*
            Here we take only one thread for workability
            actually in the array $read them often several
        */
        $stream = current($read);
        $id = array_search($stream, $this->streams);
        $this->results = stream_get_contents($this->pipes[$id][1]);
        if (feof($stream)) {
            fclose($this->pipes[$id][0]);
            fclose($this->pipes[$id][1]);
            proc_close($this->handles[$id]);
            unset($this->handles[$id]);
            unset($this->streams[$id]);
            unset($this->pipes[$id]);
        }
        return $this->results;
    }

    /*
        Static method to get the parameters of the
        command-line options
    */
    /**
     * @return bool|mixed
     */
    public static function getParams() {
        foreach ($_SERVER['argv'] as $key => $argv) {
            if ($argv == '--params' && isset($_SERVER['argv'][$key + 1])) {
                return unserialize($_SERVER['argv'][$key + 1]);
            }
        }
        return false;
    }
}

/**
 * Class ThreadsException
 * @package php\multithreading
 */
class ThreadsException extends Exception {
}
