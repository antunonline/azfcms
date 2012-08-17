<?php 
$fnc = function($a,$b){
    
};

$refl = new ReflectionFunction($fnc);
$params = $refl->getParameters();
foreach($params as $param){
    echo $param->getName();
}
?>