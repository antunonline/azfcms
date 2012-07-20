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

        return realpath(APPLICATION_PATH . "/../public/files");
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
            return $baseDir . "/" . ltrim($path, "\\/.");
            // If path is array
        } else if (is_array($path)) {
            // If path contains dirname and name keys
            if (isset($path['dirname']) && is_string($path['dirname'])
                    && isset($path['name']) && is_string($path['name'])) {

                if (strpos($path['dirname'], '..') > -1 || strpos($path['name'], '..') > -1)
                    return false;
                // Construct real path
                return rtrim($baseDir . "/" . rtrim($path['dirname'], "\\/."), "\\/.") . "/" . $path['name'];
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
        $normalizedExtensions = array();
        if(isset($filter['extensions'])&&is_array($filter['extensions'])){
            $extensions = $filter['extensions'];
            
            foreach($extensions as $extension){
                if(is_string($extension)){
                    $normalizedExtensions[] = $extension;
                } 
            }
        }
        return array(
            'directory' => isset($filter['directory']) ? ($filter['directory'] ? true : false) : (true),
            'file' => isset($filter['file']) ? ($filter['file'] ? true : false) : (true),
            'hidden' => isset($filter['hidden']) ? ($filter['hidden'] ? true : false) : (true),
            'extensions'=>$normalizedExtensions
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
        if(is_file($realPath)){
            return array();
        }
        
        
        $iterator = $this->getDirectoryIterator($realPath);
        $files = array();

        $baseDirStrLen = strlen($this->getBaseDir());
        foreach ($iterator as $file) {
            $ext = pathinfo($file->getPathname(),PATHINFO_EXTENSION);
            if ($file->isDot())
                continue;
            if ($filter['directory'] == false && $file->isDir())
                continue;
            if ($filter['hidden'] == false && substr_compare($file->getFilename(), ".", 0, 1) === 0)
                continue;
            if ($filter['file'] == false && $file->isFile())
                continue;
            if($filter['extensions']){
                $extensions = $filter['extensions'];
                if(!in_array($ext,$extensions)){
                    continue;
                }
            }

            $files[] = array(
                'dirname' => '/' . trim(substr($file->getPath(), $baseDirStrLen), "/\\."),
                'name' => $file->getBasename(),
                'date' => $file->getCTime(),
                'type' => $file->isDir()?"directory":pathinfo($file->getFilename(), PATHINFO_EXTENSION),
                'size' => $file->getSize(),
                'permissions' => substr(sprintf('%o', $file->getPerms()), -4),
                'inode' => $file->getInode()
            );
        }

        return $files;
    }

    public function getFileListMethod($directory, $filter = array()) {
        $normalizedFilter = $this->normalizeFilter($filter);
        return $this->getDirectoryFileList($directory, $normalizedFilter);
    }

    public function isPathSecure($path) {
        if (!$path)
            return false;

        $validBasePath = $this->getBaseDir();
        if (!$validBasePath) {
            return false;
        }

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
        $path = rtrim($path, "/\\.");


        foreach ($_FILES as $fileInfo) {
            if (is_string($fileInfo['tmp_name'])) {
                if (!$this->_isUploadedFile($fileInfo['tmp_name'])) {
                    continue;
                }

                $newFilePath = ($path . "/" . $fileInfo['name']);
                if (!$this->isPathSecure($newFilePath))
                    continue;

                $this->_moveUploadedFile($fileInfo['tmp_name'], $newFilePath);
            }
            else if (is_array($fileInfo['tmp_name'])) {
                for ($i = 0, $len = sizeof($fileInfo['tmp_name']); $i < $len; $i++) {
                    if (!$this->_isUploadedFile($fileInfo['tmp_name'][$i])) {
                        continue;
                    }

                    $newFilePath = ($path . "/" . $fileInfo['name'][$i]);
                    if (!$this->isPathSecure($newFilePath))
                        continue;

                    $this->_moveUploadedFile($fileInfo['tmp_name'][$i], $newFilePath);
                }
            }
        }
        return true;
    }

    public function deleteFilesMethod($files) {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * This method will delete a file on dedicated filesystem user space
     *
     * @param array $file
     * @return boolean
     */
    public function deleteFile($file) {
        if (!$this->isFileArray($file, array('dirname', 'name')))
            return false;

        $realPath = $this->constructRealPath($file);
        $isPathSecure = $this->isPathSecure($realPath);
        if (!$realPath || !$isPathSecure)
            return false;

        if (is_file($realPath)) {
            unlink($realPath);
        } else {
            $directories = array();
            $recursiveDirIterator = new RecursiveDirectoryIterator($realPath);
            $iterator = new RecursiveIteratorIterator($recursiveDirIterator, RecursiveIteratorIterator::CHILD_FIRST);
            while ($iterator->valid()) {
                /* @var $iterator RecursiveDirectoryIterator */
                if ($iterator->getFilename() == "..")
                    ;
                else if ($iterator->isDir()) {
                    $directories[] = rtrim($iterator->getPathname(), "/.");
                } else {
                    if (is_file($iterator->getPathname())) {
                        unlink($iterator->getPathname());
                    }
                }
                $iterator->next();
            }

            if ($directories) {
                usort($directories, function($first, $last) {
                            $firstCount = count_chars($first);
                            $lastCount = count_chars($last);
                            if ($firstCount[47] < $lastCount[47]) {
                                return 1;
                            } else if ($firstCount[47] > $lastCount[47]) {
                                return -1;
                            } else {
                                return 0;
                            }
                        });
                $directories = array_unique($directories);
                foreach ($directories as $rmDir) {
                    if (is_dir($rmDir)) {
                        rmdir($rmDir);
                    }
                }
            }
        }
    }

    protected function _isUploadedFile($file) {
        $isUploaded = is_uploaded_file($file);
        return $isUploaded;
    }

    protected function _moveUploadedFile($source, $destination) {
        if(file_exists($destination)){
            $path = pathinfo($destination,PATHINFO_DIRNAME);
            $basename = pathinfo($destination,PATHINFO_FILENAME);
            $ext = pathinfo($destination,PATHINFO_EXTENSION);
            
            
            $i = 1;
            do {
                $destination = $path."/".$basename."($i)".".".$ext;
                $i++;
            }while(file_exists($destination));
        }
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

    public function createDirectoryMethod($inDirectory, $name) {
        $realPath = $this->constructRealPath($inDirectory);
        if (!$this->isPathSecure($realPath))
            return false;

        $makeDir = $realPath . "/" . trim($name, "/\\");

        if (is_readable($makeDir)) {
            return true;
        }

        mkdir($makeDir, 0775, true);
        return true;
    }

    public function getRootMethod() {
        $baseDir = $this->getBaseDir();

        return $this->describeFile($baseDir);
    }

    public function describeFile($path) {
        return array(
            "name" => "",
            "dirname" => "/",
            "size" => filesize($path),
            "date" => filectime($path),
            "type" => "",
            "permissions" => fileperms($path),
            "inode" => fileinode($path)
        );
    }

    /**
     * 
     * @param string|array $directory
     * @return array|boolean
     */
    public function getParentDirectoryMethod($directory) {
        if (!$this->isFileArray($directory, array('name', 'dirname'))) {
            return false;
        }

        if (!$realPath = $this->constructRealPath($directory)) {
            return false;
        }

        if ($realPath == $this->constructRealPath($this->getBaseDir())) {
            $parentPath = $this->getBaseDir();
        } else {
            $parentPath = dirname($realPath);
        }

        if (!$this->isPathSecure($parentPath)) {
            return false;
        }

        return $this->describeFile($parentPath);
    }

}
