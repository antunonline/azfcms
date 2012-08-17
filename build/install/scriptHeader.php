<?php
error_reporting(E_ALL|E_STRICT);
ob_start();
session_start();

if(isset($_GET['clearSession'])){
    $_SESSION[InstallWorker::SESSION_BUILDER]=array();
}

$jobs = array();
$vars = array();
$resources = array();


function getQueryArg($name,$default="") {
    return isset($_GET[$name])?$_GET[$name]:$default;
}

class InstallWorker {

    const SESSION_BUILDER = "installBuilder";

    protected $_jobs;
    protected $_vars;
    protected $_resources=array();

    public function __construct(array $jobs, array $vars,array $resources) {
        $this->_jobs = $jobs;
        $this->_vars = $vars;
        $this->_resources = $resources;
        if (!isset($_SESSION[self::SESSION_BUILDER])) {
            $_SESSION[self::SESSION_BUILDER] = array();
        }
        
    }

    public function sessionSet($key, $value) {
        $_SESSION[self::SESSION_BUILDER][$key] = $value;
    }

    public function sessionGet($key, $default = false) {

        if (!isset($_SESSION[self::SESSION_BUILDER][$key])) {
            return $default;
        } else {
            return $_SESSION[self::SESSION_BUILDER][$key];
        }
    }
    
    
    public function clearSession() {
        $_SESSION = array();
        $_SESSION[self::SESSION_BUILDER] = array();
    }

    protected function _getMethodArguments(ReflectionFunction $fnc) {
        $argKeys = array();
        foreach ($fnc->getParameters() as $param) {
            $argKeys[] = $param->getName();
        }

        $arguments = array();
        foreach ($argKeys as $key) {
            $arguments[] = $this->_vars[$key];
        }

        return $arguments;
    }

    protected function _runJob($jobIndex) {
        $closure = $this->_jobs[$jobIndex];
        $fnc = new ReflectionFunction($closure);
        $arguments = $this->_getMethodArguments($fnc);
        $fnc->invokeArgs($arguments);
    }
    
    
    /**
     * 
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getResource($name,$default) {
        if(isset($this->_resources[$name])){
            return $this->_resources[$name];
        } else {
            return $default;
        }
    }
    

    public function runAll() {
        $jobs = $this->_jobs;
        $vars = $this->_vars;

        foreach ($jobs as $jobIndex => $fnc) {
            $this->_runJob($jobIndex);
        }

        // Flush the log
        $this->_vars['log']->flush();
    }

    public function runNextJob() {
        $runnedJobs = $this->sessionGet("runnedJobs", array());
        $jobDiff = array_diff(array_keys($this->_jobs), $runnedJobs);
        
        $nextJobIndex = array_shift($jobDiff);
        
        if ($nextJobIndex !== null) {
            $this->_runJob($nextJobIndex);
            $runnedJobs[] = $nextJobIndex;
            $this->sessionSet("runnedJobs", $runnedJobs);
        }
        
        
        
        return true;
    }
    
    
    public function flushResource($name) {
        if(isset($this->_resources[$name])){
            echo $this->_resources[$name];
        }
    }
    
    public function flushLogs(){
        echo $this->_vars['log']->flush();
    }

}

class InstallWorkerLog {

    protected $_lines = array();

    public function writeln($line) {
        if (is_string($line)) {
            $this->_lines[] = ($line);
        } else if ($line instanceof Exception) {
            $this->_lines[] = $line->getMessage();
        } else {
            $this->_lines[] = var_export($line, true);
        }
    }

    public function flush() {
        echo join("<br />", $this->_lines);
    }

}

?>
