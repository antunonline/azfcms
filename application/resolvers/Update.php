<?php

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Update extends Azf_Service_Lang_Resolver {

    public function getUserModel() {
        return new Azf_Model_User();
    }

    protected function isAllowed($namespaces, $parameters) {
        return Azf_Acl::hasAccess("service.update.push");
    }

    /**
     * 
     * @return Azf_Service_Lang_ResolverHelper_Dojo
     */
    public function getDojoHelper() {
        return $this->getHelper("dojo");
    }

    public function getUserPassword() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $userRecord = $this->getUserModel()->find($user['id']);
        return $userRecord->password;
    }

    /**
     * 
     *  Normalize public path, by ZF default public folder name is named public, but
     * usually on shared hosting env-s the name is www or public_html.
     * 
     * @param string $path
     * @return string
     */
    protected function _normalizePath($path) {
        if (strpos($path, "public/") == 0) {
            return str_replace("public/", getcwd() . "/", $path);
        } else {
            return $path;
        }
    }

    /**
     * 
     * @param array $newFiles
     */
    protected function _applyNewFiles(array $newFiles) {
        foreach ($newFiles as $newFile) {
            $path = $this->_normalizePath($newFile[0]);
            $body = $newFile[1];

            @mkdir($path);
            @rmdir($path);
            file_put_contents($path, $body);
        }
    }

    /**
     * 
     * @param array $files
     */
    protected function _applyModifiedFiles(array $files) {
        foreach ($files as $file) {
            $path = $this->_normalizePath($file[0]);
            $body = $file[1];
            
            if (!file_exists($path)) {
                @mkdir($path);
                @rmdir($path);
            }
            
            file_put_contents($path, $body);
        }
    }
    
    
    protected function _applyDeletedFiels(array $files) {
        foreach($files as $file){
            $path = $this->_normalizePath($file);
            if(is_dir($path)){
                rmdir($path);
            } else if(is_file($path)){
                @unlink($path);
            }
        }
    }

    /**
     * 
     * @param array $updates
     */
    public function applyUpdates(array $updates) {
        $this->_applyNewFiles($updates['new']);
        $this->_applyModifiedFiles($updates['modified']);
        $this->_applyDeletedFiels($updates['deleted']);
        
    }

    /**
     * @param string $actualBodyHash
     */
    public function pushMethod($actualBodyHash) {
        if (!isset($_POST['body']) || !is_string($_POST['body'])) {
            return $this->getDojoHelper()
                            ->createPutResponse(null, false, "Body POST variable is missing, or invalid");
        }
        $body = $_POST['body'];
        $expectedBodyHash = sha1($body . $this->getUserPassword());

        if ($expectedBodyHash !== $actualBodyHash) {
            $this->getDojoHelper()
                    ->createPutResponse(null, false, "Body hash is invalid");
        }

        $decodedBody = json_decode($body, true);
        if (!is_array($decodedBody)) {
            $this->getDojoHelper()
                    ->createPutResponse(false, false, "Body could not be deserialized!");
        }

        $this->applyUpdates($decodedBody);

        try {
            
        } catch (Exception $e) {
            $this->getDojoHelper()
                    ->createPutResponse(false, false, $e->getMessage());
        }
        $this->getDojoHelper()
                ->createPutResponse();
    }

}
