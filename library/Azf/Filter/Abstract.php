<?php

abstract class Azf_Filter_Abstract {
    /**
     * Defaults to true
     */

    const BRAKE_CHAIN_ON_FAILURE = 'brakeChainOnFailure';
    /**
     * Defaults to true
     */
    const REQUIRED = 'required';

    /**
     * Rewrites rules to data keys mappings
     */
    const FIELD = 'fields';
    
    const REMOVE = 'remove';

    /**
     * 
     */
    const ALLOW_EMPTY = "allowEmpty";
    const MESSAGES = 'messages';
    const DEFAULT_VALUE = "";
    
    const REMOVE_VALIDATORS = 'removeValidators';

    
    /**
     * 
     * @param string $name
     * @return string
     */
    protected function _getGenericRule($name) {
        $propName = "_{$name}Generic";
        return $this->{$propName};
    }

    /**
     * @return array
     */
    public function getFilterRules() {
        $filterRules = $this->_filterRules;
        $constructedRules = array();

        foreach ($filterRules as $name => $rule) {

            // If generic rule is specified
            if (is_string($rule)) {
                $constructedRules[$name] = $this->_getGenericRule($rule);
            } else {
                $constructedRules[$name] = $rule;
            }
        }

        return $constructedRules;
    }

    /**
     *  Construct filter input rules
     * @param array $overrideRules
     * @param array|null $forFields - If empty array
     */
    public function getFilterInputRules(array $overrideRules = array(),$forFields=null) {
        $filterRules = $this->getFilterRules();
        $filters = $validators = array();
        
        // Remove unwanted rules
        if(is_array($forFields)){
            $filterRules = array_intersect_key($filterRules, array_flip($forFields));
        }
        
        foreach ($filterRules as $name => $filterRule) {
            // Override if provided
            if (isset($overrideRules[$name]) && is_array($overrideRules[$name])) {
                $filterRule = $overrideRules[$name] + $filterRule;
            }
            
            if(isset($filterRule[self::REMOVE])&&$filterRule[self::REMOVE]){
                continue;
            }

            $filters[$name] = $validators[$name] = array();

            
            if (isset($filterRule['validators'])) {
                $validators[$name]+=$filterRule['validators'];
            }

            if (isset($filterRule['filters'])) {
                $filters[$name]+= $filterRule['filters'];
            }

            if (isset($filterRule[self::FIELD])) {
                $validators[$name][Zend_Filter_Input::FIELDS] = $filterRule[self::FIELD];
                $filters[$name][Zend_Filter_Input::FIELDS] = $filterRule[self::FIELD];
            }

            if (isset($filterRule[self::REQUIRED])) {
                $validators[$name][Zend_Filter_Input::PRESENCE] = $filterRule[self::REQUIRED] ?
                        Zend_Filter_Input::PRESENCE_REQUIRED : Zend_Filter_input::PRESENCE_OPTIONAL;
                $filters[$name][Zend_Filter_Input::PRESENCE] = $filterRule[self::REQUIRED] ?
                        Zend_Filter_Input::PRESENCE_REQUIRED : Zend_Filter_input::PRESENCE_OPTIONAL;
            } else {
                $validators[$name][Zend_Filter_Input::PRESENCE] = Zend_Filter_Input::PRESENCE_REQUIRED;
                $filters[$name][Zend_Filter_Input::PRESENCE] = Zend_Filter_Input::PRESENCE_REQUIRED;
            }

            if (isset($filterRule[self::ALLOW_EMPTY])) {
                $validators[$name][Zend_Filter_Input::ALLOW_EMPTY] = $filterRule[self::ALLOW_EMPTY];
            }



            if (isset($filterRule[self::DEFAULT_VALUE])) {
                $validators[$name][Zend_Filter_Input::DEFAULT_VALUE] = $filterRule[self::DEFAULT_VALUE];
            }



            if (isset($filterRule[self::MESSAGES])) {
                $validators[$name][Zend_Filter_Input::MESSAGES] = $filterRule[self::MESSAGES];
            }
            
            if(isset($filterRule[self::REMOVE_VALIDATORS]) && is_array($filterRule[self::REMOVE_VALIDATORS])){
                $removeValidators= $filterRule[self::REMOVE_VALIDATORS];
                $currentValidators = $validators[$name];
                foreach($removeValidators as $removeValidator){
                    foreach($currentValidators as $index=>$curValidator){
                        if($curValidator===$removeValidator){
                           unset($validators[$name][$index]);
                        } else if(is_array($curValidator)&&$curValidator[0]==$removeValidator) {
                             unset($validators[$name][$index]);
                        }
                    }
                }
            }
            
            
        }

        return array(
            'filters' => $filters,
            'validators' => $validators
        );
    }

    /**
     * @param array $overrideRules
     * @param array|null $forFields - If empty array
     * @return Zend_Filer_Input
     */
    public function getFilterInput(array $overrideRules=array(),$forFields=null) {
        $rules = $this->getFilterInputRules($overrideRules,$forFields);
        return new Zend_Filter_Input($rules['filters'], $rules['validators']);
    }

}
