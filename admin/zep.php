<?php
	
	$data ='toth:'.crypt("prj07")."\n";
	file_put_contents("auth/.htpasswd", $data);
	//$content =  '<Files index.php>'."\n";
	$content .=  'AuthName "Accès Restreint"'."\n";
	$content .= 'AuthType Basic'."\n";
	$content .= 'AuthUserFile "'.realpath('auth/.htpasswd').'"'."\n";
	$content .= 'Require valid-user'."\n";
	//$content .=  '</Files>'."\n";
	file_put_contents('.htaccess',$content); 
	
