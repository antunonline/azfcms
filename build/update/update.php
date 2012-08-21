<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of update
 *
 * @author antun
 */
class GitDiffBuilder {
    
    protected $_gitCommand = "git diff --name-status HEAD~1";
    
    function __construct($gitCommand=null) {
        if(is_string($gitCommand)&&(strpos($gitCommand,"git diff")===0)){
            $this->_gitCommand = $gitCommand;
        }
        
    }

        /**
     * 
     * @return type
     */
    protected function _getDiffChangeList() {
        return array_diff(explode("\n", `$this->_gitCommand`),array(""));
    }

    protected function _getDiffLinePath($line) {
        return substr($line, strpos($line, "\t") + 1);
    }

    /**
     * 
     * @param string $changeCode
     * @return array
     */
    protected function _getDiffFileList($changeCode) {
        $lines = $this->_getDiffChangeList();


        $changes = array();
        foreach ($lines as $line) {
            if ($line[0] == $changeCode) {
                $path = $this->_getDiffLinePath($line);
                $changes[] =
                        $path;
            }
        }

        return $changes;
    }

    public function _getFileRecords($changeCode) {
        $modifiedFiles = array();
        $fileList = $this->_getDiffFileList($changeCode);

        foreach ($fileList as $file) {
            $modifiedFiles[] = array(
                $file, file_get_contents($file)
            );
        }

        return $modifiedFiles;
    }

    /**
     * @return array
     */
    public function getModifiedFileRecords() {
        return $this->_getFileRecords("M");
    }

    /**
     * @return array
     */
    public function getNewFileRecords() {
        return $this->_getFileRecords("A");
    }

    public function getDeletedFileRecords() {
        return $this->_getFileRecords("D");
    }

    /**
     * 
     * @return array
     */
    public function getDiff() {
        return array(
            'new' => $this->getNewFileRecords(),
            'modified' => $this->getModifiedFileRecords(),
            'deleted' => $this->getDeletedFileRecords()
        );
    }

    public function toString() {
        return (string) $this;
    }

    public function __toString() {
        return json_encode($this->getDiff());
    }

}

class UpdateTransferService {

    protected $_targetUrl;
    protected $_loginName;
    protected $_password;
    protected $_sessionKey;
    protected $_signKey;
    protected $_updateBody;
    protected $_signedBodyHash;

    function __construct($targetUrl, $loginName, $password) {
        $this->_targetUrl = $targetUrl . "/json-lang.php";
        $this->_loginName = $loginName;
        $this->_password = sha1($password);
    }

    protected function _getSignKeyAndSessionKey() {

        $postData = http_build_query(array("expr" => "cms.user.getPasswordSignKey()"));
        $contextParams = array(
            'http' => array(
                'method' => 'POST',
                'content' => $postData,
                'header' => 'Content-Type: application/x-www-form-urlencoded'
            )
        );

        $context = stream_context_create($contextParams);
        $stream = fopen($this->_targetUrl, 'r', false, $context);
        $response = stream_get_meta_data($stream);

        $responseBody = "";
        while ($responseChunk = fread($stream, 1024)) {
            $responseBody.=$responseChunk;
        }


        $decodedResponse = json_decode($responseBody, true);
        if (!is_array($decodedResponse)) {
            throw new Exception("Sign key request failed, returned data type is not an object");
        }

        if (!$decodedResponse['status']) {
            throw new Exception("Sign key response status is not positive!");
        }

        $signKey = $decodedResponse['response'];

        $needle = "PHPSESSID=";
        $needleLen = strlen($needle);
        foreach ($response['wrapper_data'] as $header) {
            $pos = strpos($header, $needle);
            if ($pos !== false) {
                $sessionId = substr($header, $pos + $needleLen, strpos($header, ";", $pos + $needleLen) - ($pos + $needleLen));
                break;
            }
        }

        fclose($stream);

        $this->_sessionKey = $sessionId;
        $this->_signKey = $signKey;
    }

    protected function _login() {

        $signedPassword = sha1($this->_signKey . $this->_password);
        $postData = array(
            'expr' => "cms.user.login('$this->_loginName','$signedPassword')"
        );
        $encodedPostData = http_build_query($postData);

        $streamContextProps = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cookie: PHPSESSID=' . $this->_sessionKey
                ),
                'content' => $encodedPostData
            )
        );

        $context = stream_context_create($streamContextProps);
        $handle = fopen($this->_targetUrl, "r", false, $context);

        $responseBody = "";
        while ($responseChunk = fread($handle, 1024)) {
            $responseBody.=$responseChunk;
        }
        $decodedResponse = json_decode($responseBody, true);
        if (!is_array($decodedResponse)) {
            throw new Exception("Login key request failed, returned data type is not an object");
        }

        if (!$decodedResponse['status']) {
            throw new Exception("Login key response status is not positive!");
        }



        fclose($handle);
    }

    protected function _pushUpdate() {
        $signedBodyHash = sha1($this->_updateBody . $this->_password);

        $postData = array(
            'expr' => "cms.update.push('$signedBodyHash')",
            'body' => $this->_updateBody,
            'XDEBUG_SESSION_START'=>'netbeans-xdebug'
        );
        $encodedPostData = http_build_query($postData);

        $contextOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cookie: PHPSESSID=' . $this->_sessionKey . ";"
                ),
                'content' => $encodedPostData
            )
        );
        $context = stream_context_create($contextOptions);

        $handle = fopen($this->_targetUrl, 'r', false, $context);

        $response = "";
        while ($responseChunk = fread($handle, 1024)) {
            $response.=$responseChunk;
        }
        fclose($handle);

        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse)) {
            throw new Exception("Update request failed, returned data type is not an object");
        }

        if (!$decodedResponse['status']) {
            throw new Exception("Update response status is not positive, (error: $decodedResponse[errors])");
        }
    }

    public function exec($body = null) {
        if(is_string($body)){
            $this->_updateBody = $body;
        }
        try {
            $this->_getSignKeyAndSessionKey();
            $this->_login();
            $this->_pushUpdate();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

}

$diffBuilder = new GitDiffBuilder(getenv('gitCommand'));
$diff = $diffBuilder->toString();
$update = new UpdateTransferService(getenv("target"),  getenv("user"),  getenv("password"));
$update->exec($diff);