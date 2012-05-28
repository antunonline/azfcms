<?php

/**
 * Description of Descriptor
 *
 * @author antun
 */
class Azf_Template_Descriptor {

    /**
     *
     * @var array
     */
    protected $_templates;

    /**
     *
     * @var String
     */
    protected $_templateDirectoryPath;

    /**
     *
     * @var string
     */
    protected $_classPath;

    /**
     * 
     * @param array $templates
     */
    public function setTemplates(array $templates) {
        $this->_templates = $templates;
    }

    /**
     * @return array
     */
    public function getTemplates() {
        if (is_null($this->_templates)) {
            $this->_initTemplates();
        }
        return $this->_templates;
    }

    /**
     * 
     * @param string $templateIdentifier
     * @return array|null
     */
    public function getTemplate($templateIdentifier) {
        $templates = $this->getTemplates();
        if (isset($templates[$templateIdentifier])) {
            return $templates[$templateIdentifier];
        } else {
            return null;
        }
    }

    /**
     * 
     * @param string $templateIdentifier
     * @return array
     */
    public function getRegions($templateIdentifier) {
        $template = $this->getTemplate($templateIdentifier);
        if ($template == null) {
            return null;
        }
        return $template['regions'];
    }

    /**
     * @return string
     */
    public function getTemplateDirectoryPath() {
        if ($this->_templateDirectoryPath == null) {
            $this->_initTemplateDirectoryPath();
        }
        return $this->_templateDirectoryPath;
    }

    /**
     * 
     * @param string $path
     */
    public function setTemplateDirectoryPath($path) {
        $this->_templateDirectoryPath = $path;
    }

    /**
     * 
     * @return string
     */
    public function getClassPath() {
        if (is_null($this->_classPath)) {
            $this->setClassPath(__FILE__);
        }
        return $this->_classPath;
    }

    /**
     * 
     * @param string $classPath
     */
    public function setClassPath($classPath) {
        $this->_classPath = $classPath;
    }

    /**
     * 
     * @return string
     */
    public function getSchemaSource() {
        return file_get_contents(dirname($this->getClassPath()) . "/template.xsd");
    }

    /**
     * 
     * @param DOMDocument $document
     * @return array
     */
    public function templateToArray(DOMDocument $document) {
        $template = (array) simplexml_import_dom($document);
        $regions = array();
        foreach($template['regions'] as $region){
            $regions[] = (array)$region;
        }
        $template['regions'] = $regions;
        return $template;
    }

    /**
     * @return void
     */
    protected function _initTemplateDirectoryPath() {
        if (defined("APPLICATION_PATH") == false) {
            throw new RuntimeException("constant Application_Path is not defined");
        }

        $this->setTemplateDirectoryPath(realpath(APPLICATION_PATH . "/configs/descriptor/template"));
    }

    /**
     * @return void
     */
    protected function _initTemplates() {
        $potentialTemplateFilePaths = $this->_buildPotentialTemplateFilePaths();
        $parsedTemplates = $this->_parseTemplateFiles($potentialTemplateFilePaths);
        $this->setTemplates($parsedTemplates);
    }

    /**
     * @return array
     */
    protected function _buildPotentialTemplateFilePaths() {
        $templateDirectory = $this->getTemplateDirectoryPath();
        $potentialTemplateFilePath = array();

        $iterator = new DirectoryIterator($templateDirectory);

        foreach ($iterator as $item) {
            /* @var $item DirectoryIterator */
            if (strtolower($item->getExtension()) == "xml" && $item->isReadable()) {
                $potentialTemplateFilePath[] = $item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename();
            }
        }

        return $potentialTemplateFilePath;
    }

    /**
     * 
     * @param array $templateFilePaths
     * @return array
     */
    protected function _parseTemplateFiles(array $templateFilePaths) {
        $schemaSource = $this->getSchemaSource();
        $parsedTemplates = array();
        $domDocument = new DOMDocument("1.0", "utf-8");
        
        foreach ($templateFilePaths as $templateFilePath) {
            $templateSource = file_get_contents($templateFilePath);
            $domDocument->loadXML($templateSource);

            try {
                if ($domDocument->schemaValidateSource($schemaSource)) {
                    $parsedTemplate = $this->templateToArray($domDocument);
                    $parsedTemplates[$parsedTemplate['identifier']] = $parsedTemplate;
                }
            } catch (Exception $e) {
                
            }
        }

        return $parsedTemplates;
    }

}

