<?php 
//place this before any script you want to calculate time
$time_start = microtime(TRUE); 

	//Configuration file:
	
define('project_name', 'en_kosi_v3_alph');

require($_SERVER['DOCUMENT_ROOT'].project_name.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'core.php');

				
$en_kosi_core = new en_kosi_core();

for($x=1; $x<=$en_kosi_core->prod_instance_count; $x++)
{
	
	$en_kosi_core->URL_GET($en_kosi_core->prod_instance_get[$x]);
	
}
				
$time_end = microtime(TRUE);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<br> <b>Total Execution Time:</b> '.$execution_time.' Sec';
?>