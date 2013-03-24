<?php 


function smarty_function_agendastate($params, &$smarty)
{    
    switch($params['state'])
    {
    	case 1:
    		return 'durchgef&uuml;hrt';
    	case 2:
    		return 'bevorstehend';
    	case 3:
    		return 'abgesagt';
    }
    
}
