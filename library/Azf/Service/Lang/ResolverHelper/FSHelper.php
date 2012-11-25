<?php

class Azf_Service_Lang_ResolverHelper_FSHelper {
    const STATUS_SANDBOX_VIOLATION_ERROR=1;
    const STATUS_OK=2;
    const STATUS_DOES_NOT_EXIST=3;
    const STATUS_REQUEST_NOT_COMPATIBLE=7;
    const STATUS_FILE_OVERRIDE_ATTEMPT=10;
    const FILTER_DIR=4;
    const FILTER_FILE=5;
    const FILTER_NONE=6;
    const SORT_ASC=8;
    const SORT_DESC=9;
    const SORT_BY_NAME=11;
    const SORT_BY_SIZE=12;
    const SORT_BY_CTIME=13;
    const SORT_BY_MTIME=14;
    
    protected $_sandboxPath;
    protected $_publicPath;
    
    public function getSandboxPath() {
        if( ! $this->_sandboxPath){
            throw new LogicException("Sandbox path has not been defined");
        }
        return $this->_sandboxPath;
    }

    public function setSandboxPath($sandboxPath) {
        $this->_sandboxPath = realpath($sandboxPath);
    }

    public function getPublicPath() {
        if( ! $this->_publicPath ){
            throw new LogicException("Public path has not been defined");
        }
        return $this->_publicPath;
    }

    public function setPublicPath($publicPath) {
        $this->_publicPath = realpath($publicPath);
    }
    
    public function getPublicParentPath($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        $parentPath = dirname($normPath);
        $this->securePath($parentPath, true, false);
        $publicPathLen = strlen($this->getPublicPath())+1;
        
        return substr($parentPath,$publicPathLen);
    }
    
    
    public function securePath($path, $throwException = true, $normalize = true) {
        if($normalize){
            $normPath = $this->normalizePath($path);
        } else {
            $normPath = $path;
        }
        if( ! $normPath){
            throw new RuntimeException("Path $normPath does not exist",self::STATUS_DOES_NOT_EXIST);
        }
        
        $securePath = $this->getSandboxPath();
        $securePathLen = strlen($securePath);
        
        if( 0 === (strncmp($normPath,$securePath,$securePathLen))){
            return true;
        } else {
            if($throwException){
                throw new RuntimeException("Given path is not located within enforced sandbox",self::STATUS_SANDBOX_VIOLATION_ERROR);
            } else {
                return false;
            }
        }
    }

    public function normalizePath($path) {
        if(is_array($path)){
            if(isset($path['path']) && is_string($path['path'])) {
                $path = $path['path'];
            } else {
                $path = null;
            }
        } else if(!is_string($path)){
            $path = null;
        }
        return realpath($this->getPublicPath()."/".$path);
    }

    
    /**
     * 
     * @param string $path
     * @return \FilesystemIterator
     */
    protected function _getFilesystemIterator($path) {
        return new FilesystemIterator($path,  FilesystemIterator::SKIP_DOTS);
    }
    
    public function toPublicPath($path){
        $publicPath = $this->getPublicPath();
        $publicPathLen = strlen($publicPath);
        $realPath = realpath($path);
        
        return substr($realPath, $publicPathLen);
    }
    
    protected function _toArray($value) {
        if($value instanceof Iterator){
            return iterator_to_array($value,false);
        } else if(is_array($value)){
            return $value;
        } else {
            return (array) $value;
        }
    }
    
    protected function _toArraySegment($results, $start, $count) {
        $result = $this->_toArray($results);
        $totalSize = sizeof($result);
        
        $data =  array_slice($result, $start, $count);
        return array(
            'totalSize'=>$totalSize,
            'data'=>$data
        );
    }
    
    protected function _secureName($name) {
        if(!is_string($name) ||
                $name=="" ||
                (strpos($name, "..")!==false)){
            throw new RuntimeException("Given name is not valid",self::STATUS_SANDBOX_VIOLATION_ERROR);
        }
    }
    
    public function getDirectoryContents($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        $fsIterator = $this->_getFilesystemIterator($normPath);
        $directoryContents = array();
        
        foreach($fsIterator as $file){
            if($file->isDot() == false){
                $directoryContents[] = $this->toPublicPath($file->getPathname());
            }
        }
        
        return $this->_toArray($directoryContents);
    }
    
    
    public function getDetailedDirectoryContents($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        $fsIterator = $this->_getFilesystemIterator($normPath);
        $detailedIterator = new FSHelperDetailIterator($this, $fsIterator);
        return $this->_toArray($detailedIterator);
    }
    
    
    
    public function queryDetailedDirectoryContents($path, $sortField, $sortDirection, $start, $count) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        $fsIterator = $this->_getFilesystemIterator($normPath);
        $detailIterator = new FSHelperDetailIterator($this, $fsIterator);
        $sortIterator = new FSHelperSort($detailIterator, $sortField, $sortDirection);
        $result = $this->_toArray($sortIterator);
        
        return $this->_toArraySegment($result,$start,$count);
    }
    
    
    public function recursivelyQueryDetailedDirectoryContents($path, $sortField, $sortDirection, $start, $count, $filter) {
        $this->securePath($path);
        $normPath= $this->normalizePath($path);
        $fsIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($normPath,  FilesystemIterator::SKIP_DOTS));
        if(is_object($filter) && method_exists($filter, "setIterator")){
            $filter->setIterator($fsIterator);
            $detailedIterator = new FSHelperDetailIterator($this,$filter);
        } else{
            $detailedIterator = new FSHelperDetailIterator($this,$fsIterator);
        }
        
        $sortIterator = new FSHelperSort($detailedIterator, $sortField, $sortDirection);
        $data = $this->_toArraySegment($sortIterator, $start, $count);
        return $data;
    }
    
    public function getFilteredDetailedChildren($path, $filter) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        $fsIterator = $this->_getFilesystemIterator($normPath);
        
        if($filter instanceof FilterIterator){
            $detailedIterator = new FSHelperDetailIterator($filter);
        } else {
            if($filter == self::FILTER_FILE){
                $typeFilter = new FSHelperTypeFilter($fsIterator, self::FILTER_FILE);
                $detailedIterator = new FSHelperDetailIterator($this, $typeFilter);
            } else if($filter == self::FILTER_DIR){
                $typeFilter = new FSHelperTypeFilter(($fsIterator), self::FILTER_DIR);
                $detailedIterator = new FSHelperDetailIterator($this, $typeFilter);
            } else {
                $detailedIterator = new FSHelperDetailIterator($this, $fsIterator);
            }
        }
        
        return $this->_toArray($detailedIterator);
    }


    public function deleteRecursively($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        if(is_file($normPath)){
            unlink($normPath);
            return true;
        }
        
        $fsIterator = new RecursiveDirectoryIterator($normPath,  FilesystemIterator::SKIP_DOTS);
        $recIterator = new RecursiveIteratorIterator($fsIterator,  RecursiveIteratorIterator::CHILD_FIRST);
        
        while($recIterator->valid()){
            $recIterator->next();
            $path = $recIterator->getPathname();
            if(!$path)
                continue;
            if($recIterator->isDir()){
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($normPath);
        return true;
    }
    
    
    public function cloneDirectory($srcPath, $dstPath) {
        $this->securePath($srcPath);
        $this->securePath($dstPath);
        
        $normSrcPath = $this->normalizePath($srcPath);
        $normDstPath = $this->normalizePath($dstPath);
        
        $stripLen = strlen($normSrcPath)+1;
        do{
            if(isset($i)){
                $copyToPath = $normDstPath."/".basename($normSrcPath)."($i)";
            } else {
                $copyToPath = $normDstPath."/".basename($normSrcPath);
                $i = 1;
            }
            $i++;
            
        }while(file_exists($copyToPath));
        
        if(is_file($normSrcPath)){
            copy($normSrcPath, $copyToPath);
            return true;
        }
        
        $recDirIterator = new RecursiveDirectoryIterator($normSrcPath,  FilesystemIterator::SKIP_DOTS);
        $recIterator = new RecursiveIteratorIterator($recDirIterator, RecursiveIteratorIterator::SELF_FIRST);
        $newPath="";
        
        mkdir($copyToPath);
        while($recIterator->valid()){            
            $recIterator->next();
            $iteratorPath = $recIterator->getPathname();
            if(!$iteratorPath)
                continue;
            
            $newPath = $copyToPath."/".substr(realpath($iteratorPath), $stripLen);
            if($recIterator->isFile()){
                copy($iteratorPath, $newPath);
            } else {
                mkdir($newPath);
            }
        }
        
        return true;
    }
    
    public function getPathInfo($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        $splFile = new SplFileInfo($normPath);
        return FSHelperDetailIterator::splFileToArray($splFile, $this);
    }
    
    public function getFileContents($path) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        return file_get_contents($normPath);
    }
    
    
    public function createFolder($path, $name) {
        $this->securePath($path);
        $normBasePath = $this->normalizePath($path);
        $this->_secureName($name);
        $newPath = $normBasePath."/".$name;
        if(file_exists($newPath)){
            return true;
        }
        
        mkdir($newPath);
        try{
            $this->securePath($newPath,true,false);
        }catch(Exception $e){
            rmdir($newPath);
            throw $e;
        }
        
        return true;   
    }
    
    
    public function createFile($path, $name, $content) {
        $this->securePath($path);
        $this->_secureName($name);
        $normPath = $this->normalizePath($path);
        
        $newPath = $normPath."/".$name;
        
        if(realpath($newPath)){
            throw new LogicException("File override attempt at path $newPath",self::STATUS_FILE_OVERRIDE_ATTEMPT);
        }
        
        touch($newPath);
        try{
            $this->securePath($newPath,true,false);
        } catch(Exception $e){
            unlink($newPath);
            throw $e;
        }
        
        file_put_contents($newPath, $content);
        
        return true;
    }
    
    public function rename($oldPath, $newName) {
        $this->securePath($oldPath);
        $normPath = $this->normalizePath($oldPath);
        $this->_secureName($newName);
        
        $basePath = dirname($normPath);
        $newPath = $basePath."/".$newName;
        if(file_exists($newPath)){
            throw new RuntimeException(self::STATUS_FILE_OVERRIDE_ATTEMPT);
        }
        
        rename($normPath, $newPath);
        try{
            $this->securePath($newPath, true, false);
        } catch(Exception $e){
            rename($newPath, $normPath);
            throw $e;
        }
        
        return true;
    }
    
    
    public function updateFile($path, $content) {
        $this->securePath($path);
        $normPath = $this->normalizePath($path);
        
        file_put_contents($normPath, $content);
        
        return true;
    }
    
    public function move($from, $to) {
        $this->securePath($from);
        $this->securePath($to);
        
        
        $normFrom = $this->normalizePath($from);
        $normTo = $this->normalizePath($to);
        if(!is_dir($normTo)){
            throw new LogicException("Could move selection into file");
        }
        
        $toDirname = $normTo;
        $toBasename = basename($normFrom);
        
        $finalTo = $toDirname."/".$toBasename;
        $i = 0;
        while(file_exists($finalTo)){
            $finalTo = $toDirname."/".$toBasename."(".$i.")";
            $i++;
        }
        
        rename($normFrom, $finalTo);
        return true;
    }
    
    public function upload($dstPath) {
        $this->securePath($dstPath);
        $normPath = $this->normalizePath($dstPath);
        
        if(!isset($_FILES['uploadedfiles'])){
            return false;
        }
        
        $files = $_FILES['uploadedfiles'];
        if(!is_array($files['name'])){
            $normFiles = array(
                'name'=>array($files['name']),
                'size'=>array($files['size']),
                'tmp_name'=>array($files['tmp_name']),
                'error'=>array($files['error']),
                'type'=>array($files['type'])
            );
            
            $files = $normFiles;
        }
        
        $newName;
        $n = 0;
        for($len = sizeof($files['name']),$i=0;$i<$len;$i++){
            $tmpName = $files['tmp_name'][$i];
            $name = $files['name'][$i];
            $error = $files['error'][$i];
            
            if(false == is_uploaded_file($tmpName)|| $error != 0){
                continue;
            }
            
            $n = 0;
            $newName = $normPath."/".$name;
            while(file_exists($newName)){
                $newName = $normPath."/".$name."(".$n.")";
            }
            
            move_uploaded_file($tmpName, $newName);
            if(!$this->securePath($newName, false, false)){
                unlink($newName);
            }
            
        }
        
        return true;
    }
    
}



class FSHelperDetailIterator extends IteratorIterator{
    
    /**
     *
     * @var FilesystemIterator|null
     */
    protected $_fsIterator;
    protected $_fs;
    
    
    /**
     * 
     * @return Azf_Service_Lang_ResolverHelper_FSHelper
     */
    public function getFs() {
        return $this->_fs;
    }

    public function setFs(Azf_Service_Lang_ResolverHelper_FSHelper $fs) {
        $this->_fs = $fs;
    }

        
    /**
     * 
     * @param Azf_Service_Lang_ResolverHelper_FSHelper $fs
     * @param Iterator $iterator
     */
    public function __construct(Azf_Service_Lang_ResolverHelper_FSHelper $fs, $iterator) {
        parent::__construct($iterator);
        $this->setFs($fs);
        $this->_fsIterator = $iterator;
    }
    
    static function splFileToArray($splFile, $fs){
        return  array(
            'name'=>$splFile->getFilename(),
            'path'=>$fs->toPublicPath($splFile->getPathName()),
            'size'=>$splFile->getSize(),
            'ctime'=>$splFile->getCTime(),
            'mtime'=>$splFile->getMTime(),
            'isDir'=>$splFile->isDir()
        );
    }

    /**
     * 
     * @return array
     */
    public function current() {
        $file = $this->_fsIterator->current();
        $item = self::splFileToArray($file, $this->getFs());
        return $item;
    }

    public function getInnerIterator() {
        return $this->_fsIterator;
    }
}


class FSHelperSort extends SplHeap {
    const SORT_BY_NAME=11;
    const SORT_BY_SIZE=12;
    const SORT_BY_CTIME=13;
    const SORT_BY_MTIME=14;
    const SORT_ASC=8;
    const SORT_DESC=9;
    
    protected $_sortDirection;
    protected $_sortField;
    
    
    public function __construct(\Iterator $iterator, $sortField, $sortDirection) {
        $this->_sortDirection = $sortDirection;
        $this->_sortField = $sortField;
        
        foreach($iterator as $fileDetails){
            $this->insert($fileDetails);
        }
    }
    protected function compare($a, $b) {
        switch ($this->_sortField) {
            case self::SORT_BY_NAME:
                $c = $a['name'];
                $d = $b['name'];
                break;
            case self::SORT_BY_SIZE:
                $c = $a['size'];
                $d = $b['size'];
                break;
            case self::SORT_BY_CTIME:
                $c = $a['ctime'];
                $d = $b['ctime'];
                break;
            case self::SORT_BY_MTIME:
                $c = $a['mtime'];
                $d = $b['mtime'];
                break;
        }
        
        if($this->_sortField == self::SORT_BY_NAME){
            if($this->_sortDirection==self::SORT_ASC){
                return strnatcmp($d,$c);
            } else {
                return strnatcmp($c,$d);
            }
        } else {
            if($this->_sortDirection == self::SORT_ASC){
                if($c>$d){
                    return 1;
                } else {
                    return -1;
                }
            } else {
                if($d>$c){
                    return 1;
                } else {
                    return -1;
                }
            }
        }
    }
}


class FSHelperTypeFilter extends FilterIterator {
    protected $filterType;
    public function __construct(\Iterator $iterator, $type) {
        $this->filterType = $type;
        parent::__construct($iterator);
    }
    
    public function accept() {
        if($this->filterType == Azf_Service_Lang_ResolverHelper_FSHelper::FILTER_DIR){
            return ! $this->current()->isDir();
        } else if($this->filterType == Azf_Service_Lang_ResolverHelper_FSHelper::FILTER_FILE){
            return ! $this->current()->isFile();
        } else {
            return true;
        }
    }
}