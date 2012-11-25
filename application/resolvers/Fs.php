<?php

use \Azf_Service_Lang_ResolverHelper_FSHelper as FSHelper;

class Application_Resolver_Fs extends Azf_Service_Lang_Resolver {
    
    protected $_sandboxPath;
    protected $_publicPath;
    
    public function getSandboxPath() {
        return $this->_sandboxPath;
    }

    public function setSandboxPath($sandboxPath) {
        $this->_sandboxPath = $sandboxPath;
    }

    public function getPublicPath() {
        return $this->_publicPath;
    }

    public function setPublicPath($publicPath) {
        $this->_publicPath = $publicPath;
    }

    
    public function initialize() {
        parent::initialize();
        $this->setSandboxPath(APPLICATION_PATH."/../public/files");
        $this->setPublicPath(APPLICATION_PATH."/../public");
    }

    protected function isAllowed($namespaces, $parameters) {
        return Azf_Acl::hasAccess("resource.admin.rw");
    }
    
    public function handleException(Exception $e){
        switch($e->getCode()){
            case FSHelper::STATUS_DOES_NOT_EXIST:
                break;
            case FSHelper::STATUS_FILE_OVERRIDE_ATTEMPT:
                break;
            case FSHelper::STATUS_REQUEST_NOT_COMPATIBLE:
                break;
            case FSHelper::STATUS_SANDBOX_VIOLATION_ERROR:
                break;
            default:
                throw $e;
                break;
        }
        
        return $this->createDojoResponse(null, $e->getCode(), $e->getMessage());
    }

    
    /**
     * 
     * @return Azf_Service_Lang_ResolverHelper_FSHelper
     */
    public function getFsHelper() {
        $fsHelper = $this->getHelper("FSHelper");
        /* @var $fsHelper FSHelper */
        $fsHelper->setPublicPath($this->getPublicPath());
        $fsHelper->setSandboxPath($this->getSandboxPath());
        return $fsHelper;
    }
    
    public function createDojoResponse($response, $status = FSHelper::STATUS_OK, $errors = null, $metadata = null) {
        /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */
        return array(
            'data'=>$response,
            'status'=>$status,
            'errors'=>$errors,
            'metadata'=>$metadata
        );
    }
    
    public function createDojoStoreResponse($data, $totalNumOfItems) {
        return $this->createDojoResponse($data, FSHelper::STATUS_OK, null, array('total'=>$totalNumOfItems));
    }
    
    
    
    public function getDirectoryContentsMethod($path) {
        $data = $this->getFsHelper()->getDirectoryContents($path);
        return $this->createDojoResponse($data);
    }
    
    
    public function getDetailedDirectoryContentsMethod($path) {
        $response = $this->getFsHelper()->getDetailedDirectoryContents($path);
        return $this->createDojoResponse($response);
    }
    
    
    
    protected function _getQuerySortField($args){
        $return = FSHelper::SORT_BY_NAME;
        if(isset($args['sort']) && isset($args['sort'][0])){
            $sort = $args['sort'][0];
            if(isset($sort['attribute'])){
                $attribute = strtolower($sort['attribute']);
                if(in_array($attribute, array('name','size','ctime','mtime'))){
                    switch($attribute){
                        case 'size':
                            $return = FSHelper::SORT_BY_SIZE;
                            break;
                        case 'ctime':
                            $return = FSHelper::SORT_BY_CTIME;
                            break;
                        case 'mtime':
                            $return = FSHelper::SORT_BY_MTIME;
                            break;
                    }
                }
            }
        }
        
        return $return;
    }
    
    protected function _getQuerySortDirection($args) {
        $return = FSHelper::SORT_ASC;
        if(isset($args['sort']) && isset($args['sort'][0])){
            $sort = $args['sort'][0];
            if(isset($sort['descending'])){
                $descending = $sort['descending'];
                if($descending){
                    $return = FSHelper::SORT_DESC;
                }
            }
        }
        
        return $return;
    }
    
    protected function _extractQueryParams(array $args) {
        $dojoHelper = $this->getHelper("dojo"); /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */
        
        $sortField = $this->_getQuerySortField($args);
        $sortDirection = $this->_getQuerySortDirection($args);
        $start = $dojoHelper->getQueryOptionsStart($args);
        $count = $dojoHelper->getQueryOptionsCount($args);
        return array($sortField, $sortDirection, $start, $count);
    }
    
    public function queryDetailedDirectoryContentsMethod($path, array $args) {
        
        list($sortField, $sortDirection, $start, $count) = $this->_extractQueryParams($args);
        
        $data = $this->getFsHelper()->queryDetailedDirectoryContents($path, $sortField, $sortDirection, $start, $count);
        return $this->createDojoStoreResponse($data['data'], $data['totalSize']);
    }
    
    public function detailedRecursiveSearchQueryMethod($query, $args) {
        if(!is_string($query)){
            return $this->createDojoResponse(null,  FSHelper::STATUS_REQUEST_NOT_COMPATIBLE);
        }
        
        
        list($sortField, $sortDirection, $start, $count) = $this->_extractQueryParams($args);
        $filter = new \FsNameFilter($query);
        $path = "files";
        $data = $this->getFsHelper()->recursivelyQueryDetailedDirectoryContents($path, $sortField, $sortDirection, $start, $count, $filter);
        return $this->createDojoStoreResponse($data['data'], $data['totalSize']);
    }
    
    public function getChildrenMethod($path, $args) {
        if( ! $args || !is_array($args) || !isset($args['filter']) || ! (is_int($args['filter']) || ctype_digit($args['filter'])) || 
                ! ($args['filter'] > 3 && $args['filter'] < 6) ){
            $filter = FSHelper::FILTER_NONE;
        } else {
            $filter = $args['filter'];
        }
        
        $data = $this->getFsHelper()->getFilteredDetailedChildren($path, $filter);
        return $this->createDojoResponse($data);
    }
    
    protected function _getParentPath($path) {
        if(is_string($path)){
            return  substr($path, 0, strrpos($path,'/'));
        } else if(is_array($path) && isset($path['path']) && is_string($path['path'])){
            return  substr($path['path'], 0, strrpos($path['path'],'/'));
        }
        else {
            return null;
        }
    }
    
    public function getParentChildrenMethod($path, $args) {
        $parentPath = $this->_getParentPath($path);
        return $this->getChildrenMethod($parentPath, $args);
    }
    
    
    public function getPathInfoMethod($path) {
        return $this->createDojoResponse($this->getFsHelper()->getPathInfo($path));
    }
    
    public function getParentPathInfoMethod($path) {
        $parentPath = $this->_getParentPath($path);
        return $this->createDojoResponse($this->getFsHelper()->getPathInfo($parentPath));
    }
    
    
    public function recursivelyDeleteMethod($path) {
        $response = $this->getFsHelper()->deleteRecursively($path);
        return $this->createDojoResponse($response);
    }
    
    
    public function cloneDirectoryMethod($srcPath, $dstPath) {
        $response = $this->getFsHelper()->cloneDirectory($srcPath, $dstPath);
        return $this->createDojoResponse($response);
    }
    
    public function getMethod($path) {
        return $this->createDojoResponse($this->getFsHelper()->getPathInfo($path));
    }
    
    public function getFileContentsMethod($path) {
        $data = $this->getFsHelper()->getFileContents($path);
        return $this->createDojoResponse($data);
    }
    
    
    public function addMethod($name, $args) {
        if( ! is_array($args) || !isset($args['path']) || !(is_string($args['path']) || is_array($args['path'])) 
                || ! isset($args['content'])) {
            return $this->createDojoResponse(null,  FSHelper::STATUS_REQUEST_NOT_COMPATIBLE);
        }
        
        if( !is_string($args['content'])){
            $response = $this->getFsHelper()->createFolder($args['path'], $name);
        } else {
            $response = $this->getFsHelper()->createFile($args['path'], $name, $args['content']);
        }
        
        return $this->createDojoResponse($response);
    }
    
    public function getRootMethod() {
        return $this->createDojoResponse($this->getFsHelper()->getPathInfo("files"));
    }
    
    public function renameMethod($item, $newName) {
        $this->getFsHelper()->rename($item, $newName);
        return $this->createDojoResponse(true);
    }
    
    public function putMethod($path, $content) {
        if(!is_string($content)){
            return $this->createDojoResponse(null, FSHelper::STATUS_REQUEST_NOT_COMPATIBLE);
        }
        
        $this->getFsHelper()->updateFile($path, $content);
        return $this->createDojoResponse(true);
    }
    
    
    public function moveMethod($from, $to) {
        $this->getFsHelper()->move($from, $to);
        return $this->createDojoResponse(true);
    }
    
    public function uploadMethod($path){
        $this->getFsHelper()->upload($path);
        return $this->createDojoResponse(true);
    }

}



class FsNameFilter extends \FilterIterator {

    protected $_filterValue = "";
    
    public function __construct($filterName) {
        $this->_filterValue = $filterName;
    }
    
    
    public function setIterator($iterator) {
        parent::__construct($iterator);
    }
    
    
    public function accept() {
        if(!$this->_filterValue){
            return true;
        }
        
        if(stripos($this->current()->getBasename(), $this->_filterValue)===0){
            return true;
        } else {
            return false;
        }
    }
}
