<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function index1Action()
    {
        $this->_helper->viewRenderer->setNoRender(True);
        echo $this->_helper->context->getStaticParam("controller");
    }


}

