<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DojoUtil
 *
 * @author antun
 */
class Azf_Service_Lang_ResolverHelper_Dojo {

    /**
     * Extract start value from queryOptions value passed from the dojo store
     * 
     * @param mixed $value
     * @param int $default
     * @return int
     */
    public function getQueryOptionsStart($value, $default = 0) {
        switch (true) {
            case is_array($value) && isset($value['start']) &&
            (ctype_digit($value['start']) || is_int($value['start'])):
                return $value['start'];
                break;
            case is_object($value) && isset($value->start) &&
            (ctype_digit($value->start) || is_int($value->start));
                return $value->start;
            default:
                return $default;
        }
    }

    /**
     * Extract count value from queryOptions value passed from the dojo store
     * 
     * @param mixed $value
     * @param int $default
     * @return int
     */
    public function getQueryOptionsCount($value, $default = 25) {
        switch (true) {
            case is_array($value) && isset($value['count']) &&
            (ctype_digit($value['count']) || is_int($value['count'])):
                return $value['count'];
                break;
            case is_object($value) && isset($value->count) &&
            (ctype_digit($value->count) || is_int($value->count));
                return $value->count;
            default:
                return $default;
        }
    }

    /**
     * 
     * @param array|Zend_Db_Table_Rowset_Abstract $rows
     * @param mixed $queryOptions
     */
    public function sliceStoreResponse($rows, $queryOptions) {
        $start = $this->getQueryOptionsStart($queryOptions);
        $count = $this->getQueryOptionsCount($queryOptions);

        $response = array();
        /* @var $rows Zend_Db_Table_Rowset_Abstract */

        if (is_object($rows)) {
            $castRowsToArray = true;
        } else {
            $castRowsToArray = false;
        }

        for ($i = $start, $rowSize = sizeof($rows), $end = ($start + $count); $i < $rowSize && $i < $end; $i++) {
            $row = $castRowsToArray ? $rows[$i]->toArray() : $rows[$i];
            $response[] = $row;
        }

        return array(
            'total' => $rowSize,
            'data' => $response
        );
    }

    /**
     * 
     * @param array $result
     * @param int $total
     * @return array
     */
    public function constructQueryResult(array $result, $total = 0) {
        return array(
            'data' => $result,
            'total' => ((is_int($total)||  ctype_digit($total)) && $total) ? $total : sizeof($result)
        );
    }

    /**
     * 
     * @param array $params
     * @param string $key
     * @param string $default
     * @return array
     */
    public function getQueryStringParam(array $params, $key, $default = "") {
        if (isset($params[$key])) {
            $value = $params[$key];
            if (is_string($value) || is_numeric($value)) {
                return $value;
            }
        }
        return (string) $default;
    }

    /**
     * 
     * @param array $response
     * @param boolean|string|int $status
     * @param array|boolean|string $errors
     * @return array
     */
    protected function _createStdResponse($response = array(), $status = true, $errors = false) {
        return array(
            'response' => $response,
            'status' => $status,
            'errors' => $errors
        );
    }

    /**
     * 
     * @param array $response
     * @param boolean|string|int $status
     * @param array|boolean|string $errors
     * @return array
     */
    public function createPutResponse($response = array(), $status = true, $errors = false) {
        return $this->_createStdResponse($response, $status, $errors);
    }

    /**
     * 
     * @param array $response
     * @param boolean|string|int $status
     * @param array|boolean|string $errors
     * @return array
     */
    public function createGetResponse($response = array(), $status = true, $errors = false) {
        return $this->_createStdResponse($response, $status, $errors);
    }

    /**
     * 
     * @param array $response
     * @param boolean|string|int $status
     * @param array|boolean|string $errors
     * @return array
     */
    public function createAddResponse($response = array(), $status = true, $errors = false) {
        return $this->_createStdResponse($response, $status, $errors);
    }

    /**
     * 
     * @param array $response
     * @param boolean|string|int $status
     * @param array|boolean|string $errors
     * @return array
     */
    public function createRemoveResponse($response = array(), $status = true, $errors = false) {
        return $this->_createStdResponse($response, $status, $errors);
    }

    
    /**
     * 
     * Returned array will contain argumentName=>defaultValue pairs
     * 
     * @param string $regex
     * @param string $docComment
     * @return array
     */
    protected function _parseDocComment($regex, $docComment) {
        $arguments = array();

        $matches = array();
        if (preg_match_all($regex, $docComment, $matches)) {
            $matches = array_slice($matches, 1);
            for ($i = 0, $count = sizeof($matches[0]); $i < $count; $i++) {
                $arguments[$matches[0][$i]] = $matches[1][$i];
            }
        }

        return $arguments;
    }

    /**
     * This method will return associative array argumentName=>default value pairs
     * 
     * An empty array will be returned if no such argument is found
     * 
     * @param type $methodName
     */
    public function _getMethodFilterArguments($docComment) {
        $regex = "/@filter +\\$([a-z0-9_]+) *([^\n\r]+)?/i";
        return $this->_parseDocComment($regex, $docComment);
    }

    /**
     * If order by argument is found in method definition, returned array
     * will represent three key pieces of that argument:
     * 1. argument name
     * 2. acceptable argument values (as an array of values)
     * 3. default value that shall be used if the caller did not specify any value
     * 
     * Example of returned array a
     * array(
     * 'sortBy', // the name of the argument 
     * array('id','title'), // acceptable values, usually constrained by indexes
     * 'id' // Default value
     * )
     * 
     * // +++ // IMPORTANT NOTICE, if no such argument is found, empty array will be returned
     * @param string $docComment
     * @return array
     */
    protected function _getMethodOrderByArgument($docComment) {
        // @orderBy $orderBy id|title|userId|created title
        $regex = "/@orderBy +\\$([a-z0-9_]+) ([0-9a-z_|]+) +([0-9a-z_]+)/i";
        $matches = array();
        $argument = array();

        if (preg_match($regex, $docComment, $matches)) {
            $argument = array_slice($matches, 1);
            $argument[1] = explode("|", $argument[1]);
        }

        return $argument;
    }

    /**
     * This method will identify order argument name and it's default value.
     * Default value if not provided may be an empty string so we must ensure that 
     * acceptable value is passed to the method when invoked. 
     * 
     * Empty array will be returned if no such argument is found.
     * 
     * @param string $docComment
     * @return array
     */
    protected function _getMethodOrderArgument($docComment) {
        $regex = "/@order +\\$([a-z0-9_]+) *([^\n\r]+)?/i";
        return $this->_parseDocComment($regex, $docComment);
    }

    /**
     * This method will identify start argument and it's default value
     * If no default value is provided by annotation, empty string will be 
     * set as element value. 
     * 
     * If no such argument is found, empty array will be returned. 
     * 
     * @param string $docComment
     * @return array
     */
    protected function _getMethodStartArgument($docComment) {
        $regex = "/@start +\\$([a-z0-9_]+) *([^\n\r]+)?/i";
        return $this->_parseDocComment($regex, $docComment);
    }

    /**
     * This method will identify count argument and it's default value
     * If no default value is provided by annotation, empty string will be 
     * set as element value. 
     * 
     * If no such argument is found, empty array will be returned. 
     * 
     * @param string $docComment
     * @return array
     */
    protected function _getMethodCountArgument($docComment) {
        $regex = "/@count +\\$([a-z0-9_]+) *([^\n\r]+)?/i";
        return $this->_parseDocComment($regex, $docComment);
    }
    
    protected function _getReflectionClass($obj) {
        return new ReflectionClass($obj);
    }

    protected function _getReflectionMethod($model, $method) {
        $reflectionClass = $this->_getReflectionClass($model);
        return $reflectionClass->getMethod($method);
    }
    
    
    /**
     *  This method will search for @totalCall annotation defined within
     * method documentation, and if found will return preceding function identifier
     * that should when called with some or all arguments present return total number
     * of records present in db.
     * 
     * example:
     * @totalCall selectSomeByThingTotal 
     * @param string $docComment
     * @return string
     */
    protected function _getTotalFunctionName($docComment) {
        $regex = "/@totalCall +([a-z0-9_]+) *([^\n\r]+)?/i";
        $values =  $this->_parseDocComment($regex, $docComment);
        if($values){
            return array_shift(array_keys($values));
        } else {
            return false;
        }
    }
    
    

    /**
     * 
     * @param array $filterArguments
     * @param array $query
     */
    protected function _bindFilterArgumentsAndValues($filterArguments, $query) {
        $argumentValues = array();
        foreach ($filterArguments as $argumentName => $defaultValue) {
            $argumentValues[$argumentName] = $this->getQueryStringParam($query, $argumentName, $defaultValue);
        }

        return $argumentValues;
    }

    /**
     * Check if dojo submitted store sort properties are valid
     * 
     * @param array $queryArgs
     * @return boolean
     */
    protected function _isSortArgValid($queryArgs) {
        if (isset($queryArgs) && isset($queryArgs['sort']) && gettype($queryArgs['sort']) == 'array' &&
                isset($queryArgs['sort'][0])) {

            $sortRule = $queryArgs['sort'][0];
            if (is_array($sortRule) && isset($sortRule['attribute']) && is_string($sortRule['attribute']) && isset($sortRule['descending'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function _bindOrderByArgumentsAndValues($orderByArgument, $queryArgs) {
        if ($this->_isSortArgValid($queryArgs)) {
            $givenField = $queryArgs['sort'][0]['attribute'];

            if (in_array($givenField, $orderByArgument[1])) {
                $arguments[$orderByArgument[0]] = $givenField;
            }
        }

        if (!isset($arguments)) {
            $arguments[$orderByArgument[0]] = $orderByArgument[2];
        }

        return $arguments;
    }

    protected function _bindOrderArgumentsAndValues($orderArguments, $queryArgs) {
        $arguments = array();

        if ($orderArguments) {
            $argumentNames = array_keys($orderArguments);
            $argumentName = $argumentNames[0]; // Pick only first one, since currently 
            // we do not support multiple column sorts
            
            if ($this->_isSortArgValid($queryArgs)) {
                $arguments[$argumentName] = !$queryArgs['sort'][0]['descending']?'asc':'desc';
            } else {
                $arguments[$argumentName] = in_array(strtolower($orderArguments[$argumentName]), array('asc','desc'))?$orderArguments[$argumentName]:"asc";
            }
        }

        return $arguments;
    }
    
    protected function _bindStartArgumentAndValue($startArgument, $queryArgs) {
        $arguments = array();
        
        // If not empty array
        if($startArgument){
            $argumentKeys = array_keys($startArgument);
            // Extract only the first one
            $argumentName = $argumentKeys[0];
            
            $argumentValue = $this->getQueryOptionsStart($queryArgs, $startArgument[$argumentName]);
            $arguments[$argumentName] = $argumentValue;
        }
        
        return $arguments;
    }
    
    protected function _bindCountArgumentAndValue($countArgument, $queryArgs) {
        $arguments = array();
        
        // If not empty array
        if($countArgument){
            $argumentKeys = array_keys($countArgument);
            // Extract only the first one
            $argumentName = $argumentKeys[0];
            
            $argumentValue = $this->getQueryOptionsCount($queryArgs, $countArgument[$argumentName]);
            $arguments[$argumentName] = $argumentValue;
        }
        
        return $arguments;
    }
    
    
    /**
     * 
     * @param ReflectionMethod $reflectionMethod
     * @param array $arguments
     */
    protected function _callModelMethod($reflectionMethod, $model,  $arguments) {
        $argumentValues = array();
        
        $parameters = $reflectionMethod->getParameters();
        foreach($parameters as $reflectionParameter){ /* @var $reflectionParameter ReflectionParameter */          
            $argumentName = $reflectionParameter->getName();
            if(!isset($arguments[$argumentName])){
                $methodName = $reflectionMethod->getName();
                $reflectionClass = $this->_getReflectionClass($model);
                $className = $reflectionClass->getName();
                
                throw new RuntimeException("Annotation for argument '$argumentName' is defined in method {$className}->{$methodName}'");
            } else {
                $argumentValues[] = $arguments[$argumentName];
            }
        }
        
        return $reflectionMethod->invokeArgs($model, $argumentValues);
    }

    public function handleQueryRequest($model, $method, array $query, array $queryArgs, array $queryOptions) {
        $reflectionMethod = $this->_getReflectionMethod($model, $method);
        $docComment = $reflectionMethod->getDocComment();

        $filterArguments = $this->_getMethodFilterArguments($docComment);
        $orderByArgument = $this->_getMethodOrderByArgument($docComment);
        $orderArguments = $this->_getMethodOrderArgument($docComment);
        $startArgument = $this->_getMethodStartArgument($docComment);
        $countArgument = $this->_getMethodCountArgument($docComment);
        $totalMethodName = $this->_getTotalFunctionName($docComment);
        
        if(!$totalMethodName){
            $className = $this->_getReflectionClass($model)->getName();
            throw new RuntimeException("@totalCall annotation is not defined for method {$className}->{$method}");
        }


        $preparedFilterArguments = $this->_bindFilterArgumentsAndValues($filterArguments, $query);
        $preparedOrderByArguments = $this->_bindOrderByArgumentsAndValues($orderByArgument, $queryArgs);
        $preparedOrderArguments = $this->_bindOrderArgumentsAndValues($orderArguments, $queryArgs);
        $preparedStartArguments = $this->_bindStartArgumentAndValue($startArgument, $queryArgs);
        $preparedCountArguments = $this->_bindCountArgumentAndValue($countArgument,$queryArgs);
        
        $preparedArguments = $preparedFilterArguments+$preparedOrderByArguments+$preparedOrderArguments+
        $preparedStartArguments+$preparedCountArguments;
        $response = $this->_callModelMethod($reflectionMethod, $model, $preparedArguments);
        $total = $this->_callModelMethod($this->_getReflectionMethod($model, $totalMethodName), $model, $preparedArguments);
        
        return $this->constructQueryResult($response,  $total);
    }

}
