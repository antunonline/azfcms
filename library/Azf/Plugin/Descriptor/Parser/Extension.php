<?php

class Azf_Plugin_Descriptor_Parser_Extension extends Azf_Plugin_Descriptor_Parser_Abstract {
    
    

    protected function _parse($stringData) {
        $doc = $this->_getSimpleXmlElement($stringData);
        $children= $doc->children("http://it-branch.com/xml/ns/plugin/extension");
        return (array)$children;
        
        
    }

    protected function _getSchemaFileName() {
         return "Extension.xsd";
    }

}