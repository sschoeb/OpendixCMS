<?php

/**
 * 
 * 
 * @param unknown_type $params
 * @param unknown_type $smarty
 */



function smarty_function_cssmodulo($params, &$smarty)
{    
    static $last = true;
    
    $out = $params['class'];

    if($last)
    	$out = '';
    	
    $last = !$last;
   
    $smarty -> assign($params['varname'], $out);
}
