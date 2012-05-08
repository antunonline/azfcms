<?php

abstract class Azf_Plugin_Descriptor_Parser_Abstract {
    
    /**
     * String that contains xml schema
     * @var string
     */
    protected $schema;
    
    /**
     *
     * @param type $stringData 
     * @return array
     * @throws 
     */
    abstract protected function _parse($stringData);
    
    /**
     * @return string 
     */
    abstract protected function _getSchemaFileName();
    
    
    /**
     *
     * @param type $fd 
     * @throws Azf_Plugin_Descriptor_Parser_Exception
     */
    public function parseFileDescriptor($fd){
        
        // Read file content
        $content = "";
        while(false != ($read = fread($fd))){
            $content.=$read;
        }
        
        return $this->_handle($content);
    }
    
    
    /**
     *
     * @param string $filePath 
     * @throws Azf_Plugin_Descriptor_Parser_Exception
     */
    public function parseFile($filePath){
        if(!is_file($filePath)||!is_readable($filePath)){
            throw new InvalidArgumentException("File located at ".htmlentities($filePath)." is not readable");
        }
        
        $content  = file_get_contents($filePath);
        return $this->_handle($content);
    }
    
    /**
     *
     * @return string
     */
    protected function _getSchema(){
        if($this->schema!==null){
            return $this->schema;
        } else {
            // Load schema
            $this->schema = file_get_contents(__DIR__."/".$this->_getSchemaFileName());
            return $this->schema;
        }
    }
    
    
    /**
     *
     * @return \DomDocument 
     */
    protected function _getDomDocument(){
        return new DomDocument();
    }
    
    /**
     * 
     * @param string $content 
     * @return SimpleXMLElement
     */
    protected function _getSimpleXmlElement($content){
        return simplexml_load_string($content);
    }
    
    protected function _handle($content){
        $domDocument = $this->_getDomDocument();
        $domDocument->loadXML($content);
        if(!$domDocument->schemaValidateSource($this->_getSchema())) {
            throw new Azf_Plugin_Descriptor_Parser_Exception("Schema validation error");
        }
        
        return $this->_parse($content);
    }
}
