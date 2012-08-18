<?php
define("DEFINE_BUILD_DEPS",true);

class InstallScriptBuilder{
    protected $_globals = array();
    protected $_scripts = array();
    protected $_scriptLabels = array();
    protected $_resources=array();
    
    protected $_installScript="";
    
    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function addVar($name,$value) {
        $this->_globals[$name] = $value;
    }
    
    public function addVarFromFile($name,$file) {
        $this->_globals[$name] = file_get_contents(__DIR__."/".$file);
    }
    
    public function addHeader($script) {
        $this->_scripts[] = $script;
    }
    
    public function addFooter($script) {
        $this->_scripts[] = $script;
    }
    
    public function addVarFromOpt($name) {
        $argv = $GLOBALS['argv'];
        $searchFor = "--$name";
        
        for($i=1,$len=sizeof($argv);$i<$len;$i++){
            $arg = $argv[$i];
            if(strpos($arg, $searchFor)===false){
                continue;
            }
            
            $this->addVar($name, substr($arg,strpos($arg,"=")+1));
            return;
        }
        
        throw new Exception("CLI argument --$name is not provided");
    }
    
    
    /**
     * 
     * @param string $script
     */
    public function addScript($displayName, $script) {
        $scriptPath = __DIR__."/scripts/".$script.".php";
        if(!file_exists($scriptPath)){
            throw new Exception("Script $script does not exists");
        }
        
        include($scriptPath);
        
        $this->_scripts[] = file_get_contents($scriptPath);
        $this->_scriptLabels[] = $displayName;
    }
    
    
    public function addResource($name,$value) {
        $this->_resources[$name] = $value;
    }
    
    
    /**
     * @return string
     */
    public function buildScript() {
        $scripts = $this->_scripts;
        $header = array_shift($scripts);
        array_unshift($scripts, "<?php\n\$vars=".var_export($this->_globals, true).";\n?>");
        array_unshift($scripts, "<?php\n\$resources=".var_export($this->_resources, true).";\n?>");
        array_unshift($scripts, $header);
        
        return join("",$scripts);
    }
    
    public function saveScript($file) {
        $script = $this->buildScript();
        file_put_contents($file, $script);
    }
    
    public function loadIniBuildSchedule() {
        $buildSchedule = parse_ini_file("build.ini", true);
        
        foreach($buildSchedule as $job){
            $this->addScript($job['name'], $job['script']);
        }
    }
}


class InstallScriptLogger {
    protected $_lines = array();
    
    public function writeln($line) {
        if(is_string($line)){
            $this->_lines[] = ($line);
        } else if($line instanceof Exception){
            $this->_lines[] = $line->getMessage();
        } else {
            $this->_lines[] = var_export($line, true);
        }
    }
}

$sb = new InstallScriptBuilder();
$sb->addVarFromFile("dbDDL", "../resources/DDL.sql");
$sb->addVarFromFile("dbDML", "../resources/DML.sql");
$sb->addVarFromOpt("dbHost");
$sb->addVarFromOpt("dbUser");
$sb->addVarFromOpt("dbPassword");
$sb->addVarFromOpt("dbName");
$sb->addVar("appZipPath","http://web-izgradnja.com/app.zip");


$sb->addHeader(file_get_contents("scriptHeader.php"));
$sb->loadIniBuildSchedule();
$sb->addFooter(file_get_contents("scriptFooter.php"));

$sb->saveScript("installScript.php");