<?php

abstract class Azf_Rest_Provider_DojoStore extends Azf_Rest_Provider_Abstract {

    const F_CONTAIN = "contains";
    const F_EQUALS = "equals";
    const F_STARTS_WITH = "startsWith";
    const F_ENDS_WITH = "endsWith";
    const F_NOT_CONTAIN = "notContains";
    const F_NOT_START_WITH = "notStartsWith";
    const F_NOT_END_WITH = "notEndsWith";
    const F_IS_EMPTY = "isEmpty";

    /**
     *
     * @var int
     */
    protected $requestFrom = 0;

    /**
     * @var int
     */
    protected $requestCount = 20;

    /**
     * @var array
     */
    protected $sortFields = array();

    /**
     *
     * @var array 
     */
    protected $filterFields = array();

    /**
     *
     * @return int
     */
    public function getRequestFrom() {
        return $this->requestFrom;
    }

    /**
     *
     * @return int
     */
    public function getRequestCount() {
        return $this->requestCount;
    }

    public function setContentRange($from, $count, $available) {
        $to = $from + $count;
        header("Content-Range: $from-$to/$available");
    }

    protected function _initRangeHeader() {
        // Set page size
        $this->requestCount = $this->getMaxPageSize();

        if (isset($_SERVER['HTTP_RANGE'])) {
            $header = str_replace("items=", "", $_SERVER['HTTP_RANGE']);
            $chunks = explode("-", $header);
            if (count($chunks) == 2) {
                $from = $chunks[0];
                $to = $chunks[1];
                $count = $to - $from;
                if (ctype_digit($from) && ctype_digit($count)) {
                    return;
                } else if ($from < 0 || $count > $this->getMaxPageSize() || $count < 1) {
                    return;
                } else {
                    $this->requestFrom = $from;
                    $this->requestCount = $count;
                }
            }
        }
    }

    /**
     * Parse sort instruction send by REST client
     */
    public function _parseSortFields() {
        $query = $this->getRequest()->getRequestArg("QUERY_STRING");
        $sortableFields = $this->getSortableFields();

        if (false === ($start = strpos($query, "sort(")) || false === ($end = strpos($query, ")", $start))) {
            return;
        }
        $start +=5; // Add "sort("  strlen
        $sort = substr($query, $start, $end - $start);

        $parts = explode(",", $sort);

        foreach ($parts as $part) {
            $direction = $part[0] == "+" || $part[0] == "-" ? $part[0] : null;
            $field = substr($part, 1);
            $field = ctype_alpha($field) ? $field : null;

            if (!$part || !$direction) {
                continue;
            }
            if (!in_array($field, $sortableFields)) {
                continue;
            }
            $this->sortFields[$field] = $direction;
        }
    }

    protected function _parseFilterFields() {
        $queryString = $this->getRequest()->getRequestArg("QUERY_STRING");
        // Remove prefix
        $queryString = urldecode(substr($queryString, strpos($queryString, "?")));
        // Extract parts
        $parts = explode("&", $queryString);
        // Allowed field names
        $allowedFieldNames = $this->getFilterableFields();

        // Parse parts
        foreach ($parts as $part) {
            // If filter contains filter type
            if(false !== ($pos = strpos($part,":"))){
                $type = substr($part,0,$pos);
                // Default definition is F_EQUALS
                if(!in_array($type, array(self::F_CONTAIN,self::F_ENDS_WITH, self::F_EQUALS, self::F_IS_EMPTY,
                    self::F_NOT_CONTAIN, self::F_NOT_END_WITH, self::F_NOT_START_WITH, self::F_STARTS_WITH))){
                    $type=self::F_EQUALS;
                } else { 
                    // Otherwise $type equals to value provided by the request
                }
            }else { // No filter type is specified, equals will be used
                $pos = 0;
                $type = self::F_EQUALS;
            }
            
            $fieldName = substr($part, $pos+1, strpos($part,"=")-$pos-1);
            if(!in_array($fieldName,$allowedFieldNames)){
                // If field name is invalid
                continue;
            }
            
            $values = explode("=",$part);
            $value = array_pop($values);
            
            $this->filterFields[] = array(
                $fieldName, $type, $value
            );
            
        }
    }

    public function init() {
        parent::init();
        $this->_initRangeHeader();
        $this->_parseSortFields();
        $this->_parseFilterFields();
    }

    /**
     * This method will return an array of field names which
     * will validate client submitted sort field names
     * Valid sort fields will be available to the REST handling method
     * through $this->sortFields array property. 
     * 
     * @return array 
     */
    public function getSortableFields() {
        return array();
    }
    
    
    /**
     * The array value returned by this method will filter input field names which are set
     * to filter result set by provided value.
     * @return array
     */
    public function getFilterableFields(){
        return array();
    }

    /**
     * By overriding this method you can alter maximum page size. Default is 40 records per request.
     *
     * @return int 
     */
    public function getMaxPageSize() {
        return 40;
    }
    

    

}
