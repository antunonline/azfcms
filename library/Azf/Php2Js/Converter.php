<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Converter
 *
 * @author antun
 */
class Azf_Php2Js_Converter {

    public function convertFile($filePath) {
        $currentLoadedClasses = get_declared_classes();
        $this->requireFile($filePath);
        $newLoadedClasses = get_declared_classes();

        $classDelta = array_diff($newLoadedClasses, $currentLoadedClasses);
        if (sizeof($classDelta) == 0) {
            return "";
        } else {
            $className = array_pop($classDelta);
        }

        $classSources = array();
        do {
            $propertyList = $this->getPropertyList($className);
            $methodList = $this->getMethodList($className);

            $classSources[] = $this->generateJs($className, $propertyList, $methodList);
        } while ($className = array_pop($classDelta));
        return join("\n\n", $classSources);
    }

    
    
    public function convertClass($className) {
        $propertyList = $this->getPropertyList($className);
        $methodList = $this->getMethodList($className);

        return $this->generateJs($className, $propertyList, $methodList);
    }

    protected function requireFile($filePath) {
        require_once($filePath);
    }

    /**
     *
     * @param string $className
     * @return array
     */
    protected function getPropertyList($className) {
        $reflectionClass = $this->getReflectionClass($className);
        $reflectionProperties = $reflectionClass->getProperties();

        $returnProps = array();
        for ($i = 0, $iLen = sizeof($reflectionProperties); $i < $iLen; $i++) {
            $returnProps[] = $reflectionProperties[$i]->getName();
        }

        return $returnProps;
    }

    protected function getMethodList($className) {
        $reflectionClass = $this->getReflectionClass($className);
        $reflectionMethods = $reflectionClass->getMethods();

        $returnMethods = array();
        $returnMethod = array();
        for ($i = 0, $iLen = sizeof($reflectionMethods); $i < $iLen; $i++) {
            $returnMethod = array(
                'name' => $reflectionMethods[$i]->getName(),
                'parameters' => array()
            );

            $reflectionParams = $reflectionMethods[$i]->getParameters();
            for ($i1 = 0, $i1Len = sizeof($reflectionParams); $i1 < $i1Len; $i1++) {
                $returnMethod['parameters'][] = $reflectionParams[$i1]->getName();
            }
            $returnMethods[] = $returnMethod;
        }
        return $returnMethods;
    }

    protected function getReflectionClass($className) {
        static $class;
        if (!isset($class) || $class->getName() != $className) {
            $class = new ReflectionClass($className);
        }

        return $class;
    }

    protected function generateJs($className, $propertyList, $methodList) {
        $jsSource = "";
        $jsSource.="$className = {\n";

        $jsPropertySources = array();
        for ($i = 0, $iLen = sizeof($propertyList); $i < $iLen; $i++) {
            $jsPropertySources[] = $this->generateJsProperty($propertyList[$i]);
        }

        $jsMethodSoruces = array();
        for ($i = 0, $iLen = sizeof($methodList); $i < $iLen; $i++) {
            $jsMethodSoruces[] = $this->generateJsMethod($methodList[$i]);
        }

        if ($jsPropertySources) {
            $jsSource.=join(",\n", $jsPropertySources);
        }

        if (sizeof($jsPropertySources) > 0 && sizeof($jsMethodSoruces) > 0) {
            $jsSource.=",\n";
            $jsSource.=join(",\n", $jsMethodSoruces);
        }

        $jsSource.="\n}";
        return $jsSource;
    }

    /**
     *
     * @param string $propertyName
     * @return string
     */
    protected function generateJsProperty($propertyName) {
        if (in_array($propertyName, array('default', 'class', 'where'))) {
            $propertyName = "$" . $propertyName;
        }
        if($propertyName[0]=="_"){
            $propertyName = '$'.$propertyName;
        }
        return "$propertyName:null";
    }

    /**
     *
     * @param array $method
     * @return string
     */
    protected function generateJsMethod(array $method) {
        for ($i = 0, $iLen = sizeof($method['parameters']); $i < $iLen; $i++) {
            if (in_array($method['parameters'][$i], array('default', 'class', 'where'))) {
                $method['parameters'][$i] = "$" . $method['parameters'][$i];
            }
        }

        if (in_array($method['name'], array('delete'))) {
            $method['name'] = "$" . $method['name'];
        }
        if($method['name'][0]=="_"){
            $method['name'] = '$'.$method['name'];
        }

        return "$method[name]:function(" . join(",", $method['parameters']) . "){}";
    }

}
