<?php

class Application_Resolver_Filesystem extends Azf_Service_Lang_Resolver {

    public function initialize() {
        parent::initialize();
    }
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

    /**
     *
     * @var string|null
     */
    protected $_baseDir;

    public function setBaseDir($path) {
        $this->_baseDir = $path;
    }

    public function getBaseDir() {
        if ($this->_baseDir)
            return $this->_baseDir;

        return APPLICATION_PATH . "/../public/files";
    }

    public function constructRealPath($path) {
        $baseDir = $this->getBaseDir();
        // If path is string
        if (is_string($path)) {
            // If path does contain invalid characters
            if (strpos($path, "..") > -1) {
                return null;
            }
            // Return full path
            return $baseDir . "/" . ltrim($path,"\\/.");
            // If path is array
        } else if (is_array($path)) {
            // If path contains dirname and name keys
            if (isset($path['dirname']) && is_string($path['dirname'])
                    && isset($path['name']) && is_string($path['name'])) {

                if (strpos($path['dirname'], '..') > -1 || strpos($path['name'], '..') > -1)
                    return false;
                // Construct real path
                return rtrim($baseDir . "/" . rtrim($path['dirname'],"\\/."),"\\/.") . "/" . $path['name'];
            } else {
                // If array is invalid
                return null;
            }
        }
        // If no valid value is provided
        else {
            return null;
        }
    }

    public function normalizeFilter($filter) {
        return array(
            'directory' => isset($filter['directory']) ? ($filter['directory'] ? true : false) : (true),
            'file' => isset($filter['file']) ? ($filter['file'] ? true : false) : (true),
            'hidden' => isset($filter['hidden']) ? ($filter['hidden'] ? true : false) : (true)
        );
    }

    /**
     *
     * @param type $path 
     * @return DirectoryIterator
     */
    public function getDirectoryIterator($path) {
        return new DirectoryIterator($path);
    }

    public function getDirectoryFileList($directory, $filter) {
        $realPath = $this->constructRealPath($directory);

        if ($this->isPathSecure($realPath) == false) {
            return array();
        }
        $iterator = $this->getDirectoryIterator($realPath);
        $files = array();

        $baseDirStrLen = strlen($this->getBaseDir());
        foreach ($iterator as $file) {
            if($file->isDot())
                continue;
            if ($filter['directory'] == false && $file->isDir())
                continue;
            if ($filter['hidden'] == false && substr_compare($file->getFilename(), ".", 0, 1) === 0)
                continue;
            if ($filter['file'] == false && $file->isFile())
                continue;

            $files[] = array(
                'dirname' => '/'.trim(substr($file->getPath(),$baseDirStrLen), "/\\."),
                'name' => $file->getBasename(),
                'date' => $file->getCTime(),
                'type' => pathinfo($file->getFilename(), PATHINFO_EXTENSION),
                'size' => $file->getSize(),
                'permissions' => substr(sprintf('%o', $file->getPerms()), -4)
            );
        }

        return $files;
    }

    public function getFileListMethod($directory, $filter=array()) {
        $normalizedFilter = $this->normalizeFilter($filter);
        return $this->getDirectoryFileList($directory, $normalizedFilter);
    }

    public function isPathSecure($path) {
        if(!$path)
            return false;
        
        $validBasePath = $this->getBaseDir();

        if (substr_compare($path, $validBasePath, 0, strlen($validBasePath)) == 0 && strpos($path, "/../") === false) {
            return true;
        } else {
            return false;
        }
    }

    public function uploadFilesMethod($dirname) {
        $path = $this->constructRealPath($dirname);
        if (!$this->isPathSecure($path)) {
            return false;
        }
        $path = rtrim($path,"/\\.");


        foreach ($_FILES as $fileInfo) {
            if (!$this->_isUploadedFile($fileInfo['tmp_name'])) {
                continue;
            }

            $newFilePath = ($path. "/" . $fileInfo['name']);
            if (!$this->isPathSecure($newFilePath))
                continue;

            $this->_moveUploadedFile($fileInfo['tmp_name'], $newFilePath);
        }
        return true;
    }

    public function deleteFilesMethod($files) {
        foreach($files as $file){
            $this->deleteFile($file);
        }
    }
    
    
    /**
     * This method will delete a file on dedicated filesystem user space
     *
     * @param array $file
     * @return boolean
     */
    public function deleteFile($file){
        if(!$this->isFileArray($file, array('dirname','name')))
                return false;
        
        $realPath = $this->constructRealPath($file);
        $isPathSecure = $this->isPathSecure($realPath);
        if(!$realPath || !$isPathSecure)
            return false;
        
        unlink($realPath);
    }

    protected function _isUploadedFile($file) {
        $isUploaded = is_uploaded_file($file);
        return $isUploaded;
    }

    protected function _moveUploadedFile($source, $destination) {
        move_uploaded_file($source, $destination);
    }

    protected function isFileArray($file, array $requiredKeys = array()) {
        if (!is_array($file))
            return false;

        $validKeys = array(
            'dirname', 'name', 'date', 'type', 'size', 'permissions'
        );

        if (!$requiredKeys)
            $requiredKeys = $validKeys;

        $requiredKeys = array_intersect($requiredKeys, $validKeys);

        foreach ($requiredKeys as $key) {
            if (!isset($file[$key]))
                return false;
        }
        return true;
    }
    
    
    public function createDirectoryMethod($directory){
        $realPath = $this->constructRealPath($directory);
        if(!$this->isPathSecure($realPath))
            return false;
        
        mkdir($realPath,"0777",true);
        return true;
    }

}
