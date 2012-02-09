<?php

/**
 * Description of Server
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Rpc_Server {

    /**
     *
     * @var Zend_Json_Server
     */
    protected $rpcServer;

    /**
     *
     * @return Zend_Json_Server
     */
    public function getRpcServer() {
        return $this->rpcServer;
    }

    /**
     *
     * @param Zend_Json_Server $rpcServer 
     */
    public function setRpcServer(Zend_Json_Server $rpcServer) {
        $this->rpcServer = $rpcServer;
    }

    function __construct() {
        $this->_init();
    }

    /**
     * This method will initialize all dependencies required by the server 
     */
    protected function _init() {
        $rpcServer = new Zend_Json_Server();
        $this->setRpcServer($rpcServer);
    }

    public function handle() {
        $server = $this->getRpcServer();
        
        // If it is a GET request, return SMD
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            $server->setTarget('/json-rpc.php')
                    ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
            $smd = $server->getServiceMap();

            // Set Dojo compatibility:
            $smd->setDojoCompatible(true);

            header('Content-Type: application/json');
            echo $smd;
        }
        // If it is a POST request, try to invoke the service
        else {
            $server->getRequest()->getMethod();
        }
    }

}

