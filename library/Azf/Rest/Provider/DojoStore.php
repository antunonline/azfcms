<?php

class Azf_Rest_Provider_DojoStore extends Azf_Rest_Provider_Abstract {

    const F_CONTAINS = "contains";
    const F_EQUALS = "equals";
    const F_STARTS_WITH = "startsWith";
    const F_ENDS_WITH = "endsWith";
    const F_NOT_CONTAIN = "notContain";
    const F_NOT_START_WITH = "notStartWith";
    const F_NOT_END_WITH = "notEndWith";
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
     *
     * @return int 
     */
    public function getMaxPageSize() {
        return 40;
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

    public function init() {
        parent::init();
        $this->_initRangeHeader();
        $this->_parseSortFields();
    }

    /**
     *
     * @param Azf_Rest_Request $request
     * @param Azf_Rest_Response $response 
     */
    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    /**
     *
     * @param Azf_Rest_Request $request
     * @param Azf_Rest_Response $response 
     */
    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    /**
     *
     * @param Azf_Rest_Request $request
     * @param Azf_Rest_Response $response 
     */
    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    /**
     *
     * @param type $request
     * @param type $method
     * @param type $id 
     */
    public function isAllowed($request, $method, $id) {
        
    }

    /**
     *
     * @param Azf_Rest_Request $request
     * @param Azf_Rest_Response $response 
     */
    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    /**
     *
     * @param Azf_Rest_Request $request
     * @param Azf_Rest_Response $response 
     */
    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}
