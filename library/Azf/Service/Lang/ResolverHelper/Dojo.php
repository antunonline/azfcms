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
        switch(true){
            case is_array($value)&&isset($value['start'])&&
                (ctype_digit($value['start'])||is_int($value['start'])):
                    return $value['start'];
                break;
            case is_object($value)&&isset($value->start)&&
                (ctype_digit($value->start)||is_int($value->start));
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
        switch(true){
            case is_array($value)&&isset($value['count'])&&
                (ctype_digit($value['count'])||is_int($value['count'])):
                    return $value['count'];
                break;
            case is_object($value)&&isset($value->count)&&
                (ctype_digit($value->count)||is_int($value->count));
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
    public function sliceStoreResponse($rows, $queryOptions){
        $start = $this->getQueryOptionsStart($queryOptions);
        $count = $this->getQueryOptionsCount($queryOptions);
        
        $response = array();
        /* @var $rows Zend_Db_Table_Rowset_Abstract */
        
        if(is_object($rows)){
            $castRowsToArray = true;
        } else {
            $castRowsToArray = false;
        }
        
        for($i=$start,$rowSize = sizeof($rows),$end=($start+$count);$i<$rowSize&&$i<$end;$i++){
            $row = $castRowsToArray?$rows[$i]->toArray():$rows[$i];
            $response[] = $row;
        }
        
        return array(
            'total'=>$rowSize,
            'data'=>$response
        );
    }
    
    
    
    /**
     * 
     * @param array $result
     * @param int $total
     * @return array
     */
    public function constructQueryResult(array $result, $total=0) {
       return array(
           'data'=>$result,
           'total'=>  (is_int($total)&&$total)?$total:sizeof($result)
       ) ;
    }
}
