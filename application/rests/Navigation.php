<?php

/**
 * 
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Application_Rest_Navigation extends Azf_Rest_Provider_Abstract {

    public function isAllowed($request, $method, $id) {
        return Azf_Acl::hasAccess("resource.admin.rw");
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $navigation = Zend_Registry::get("navigationModel");
        /* @var $navigation Azf_Model_Tree_Navigation */
        $record = $navigation->getChildren($request->getId(),array('id','parentId','l','r','title','url','disabled','home'));
        return $record;
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $navigation = Zend_Registry::get("navigationModel");
        /* @var $navigation Azf_Model_Tree_Navigation */
        $rows = $navigation->getTree();
        return array($rows);
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}

