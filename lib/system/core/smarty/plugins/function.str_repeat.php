<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     str_repeat
 * Author:   Ken Stanley <smarty@kennethpaul.com>
 * Purpose:  print out a value x amount of times
 * -------------------------------------------------------------
 */
function smarty_function_str_repeat($params, &$smarty)
{
    static $string = array();
    static $count = array();
    static $assign = "";

    extract($params);
   
    
    
    if (!empty($assign))
    {
    	
        $smarty->assign($assign, str_repeat($string[0],$count[0]));
    }
    else
    {
        echo str_repeat($string,$count);
    }
}