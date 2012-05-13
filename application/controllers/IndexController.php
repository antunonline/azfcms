<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->navigation = new Application_View_Helper_Navigation();
        $this->view->navigation->setView($this->view);
    }

    public function index1Action()
    {
        $this->_helper->viewRenderer->setNoRender(True);
        echo $this->_helper->context->getStaticParam("controller");
    }


}

