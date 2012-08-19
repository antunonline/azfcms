<?php

define("DEFINE_BUILD_DEPS", true);

class InstallScriptBuilder {

    protected $_globals = array();
    protected $_scripts = array();
    protected $_scriptLabels = array();
    protected $_resources = array();
    protected $_installScript = "";

    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function addVar($name, $value) {
        $this->_globals[$name] = $value;
    }

    public function addVarFromFile($name, $file) {
        $this->_globals[$name] = file_get_contents(__DIR__ . "/" . $file);
    }

    public function addHeader($script) {
        $this->_scripts[] = $script;
    }

    public function addFooter($script) {
        $this->_scripts[] = $script;
    }

    /**
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOpt($name, $default = null) {
        $argv = $GLOBALS['argv'];
        $searchFor = "--$name";

        for ($i = 1, $len = sizeof($argv); $i < $len; $i++) {
            $arg = $argv[$i];
            if (strpos($arg, $searchFor) === false) {
                continue;
            }

            return substr($arg, strpos($arg, "=") + 1);
        }

        return $default;
    }

    public function addVarFromOpt($name) {
        $value = $this->getOpt($name);
        
        if (!$value) {
            if(!isset($this->_globals[$name])){
                throw new Exception("CLI argument --$name is not provided");
            }
            return;
            
        } else {
            $this->_globals[$name] = $value;
        }
    }

    /**
     * 
     * @param string $script
     */
    public function addScript($displayName, $script) {
        $scriptPath = __DIR__ . "/scripts/" . $script . ".php";
        if (!file_exists($scriptPath)) {
            throw new Exception("Script $script does not exists");
        }

        include($scriptPath);

        $this->_scripts[] = file_get_contents($scriptPath);
        $this->_scriptLabels[] = $displayName;
    }

    public function addResource($name, $value) {
        $this->_resources[$name] = $value;
    }

    /**
     * @return string
     */
    public function buildScript() {
        $scripts = $this->_scripts;
        $header = array_shift($scripts);
        array_unshift($scripts, "<?php\n\$vars=" . var_export($this->_globals, true) . ";\n?>");
        array_unshift($scripts, "<?php\n\$resources=" . var_export($this->_resources, true) . ";\n?>");
        array_unshift($scripts, $header);

        return join("", $scripts);
    }

    public function saveScript($file) {
        $script = $this->buildScript();
        file_put_contents($file, $script);
    }

    protected function _importIniBuildVarGroup($groups) {

        foreach ($groups as $groupName => $group) {
            if ($groupName == ":vars") {
                foreach ($group as $key => $value) {
                    $this->addVar($key, $value);
                }
                break;
            }
        }
    }

    public function loadIniBuildSchedule() {
        $buildScript = $this->getOpt("build", "build");
        $buildSchedule = parse_ini_file("buildConfigurations/$buildScript.ini", true);

        $this->_importIniBuildVarGroup($buildSchedule);

        foreach ($buildSchedule as $groupName => $job) {
            if ($groupName[0]==":") {
                continue;
            }
            $this->addScript($job['name'], $job['script']);
        }
    }

}

class InstallScriptLogger {

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

}

$sb = new InstallScriptBuilder();


$sb->addHeader(file_get_contents("scriptHeader.php"));
$sb->loadIniBuildSchedule();
$sb->addFooter(file_get_contents("scriptFooter.php"));

$sb->saveScript("installScript.php");