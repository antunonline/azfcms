<?php

class Azf_Plugin_Descriptor_Parser_Content extends Azf_Plugin_Descriptor_Parser_Abstract {
    
    const NAME = "name";
    const DISPLAY_NAME = "displayName";
    const DESCRIPTION="description";
    const VERSION = "version";

    protected function _parse($stringData) {
        $doc = $this->_getSimpleXmlElement($stringData);
        $children = $doc->children("http://it-branch.com/xml/ns/plugin/content");
        return (array)$children;
    }

    protected function _getSchemaFileName() {
        return "Content.xsd";
    }

}