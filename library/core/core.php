<?php
/* 
CREATED BY DAVID "CRONOS88" MABASO

@var prod_instance_count
@var prod_instance_rotate
@var prod_instances
@var i
@prod_instance_staging
@path
@prod1 array()
@prod2 array()
@prod_check
Anything and everything runs through the core 
as it is the main logic of the entire app

 */
 //Please activate once done testing
//define('project_name', 'en_kosi_v3_alph');



require($_SERVER['DOCUMENT_ROOT'].project_name.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'config.php');

class en_kosi_core
{
			
	public $prod_check = array();
	
	public $prod_instance_get = NULL;
	
	// Prod instances list_num
	public $prod_instance_count = NULL;
	
	private $instances_strip_host = NULL;
	
	private $instances_strip_port = NULL;
	
	// Prod instances list: URL batch job initialisation
	
	private $prod_instance_rotate = NULL;	
	
	private $prod_instances = NULL;	
	
	public $path = '/';
	
	private $prod1 = array();
				
	private $prod2 = array();
	
	private $prod_instance_staging = 'prod1';
	
	private $prod_instance_strip_host = NULL;
	
	private $prod_instance_strip_port = NULL;
		
	private $prod_instance_messages = NULL;
	
	private $prod_instance_URL = NULL;
	
	//Writes to alerts DB
	private $prod_instance_messages_alerts_int = NULL;
	
	//Image render and create
	private $images_URL = NULL;
	
	private $download = FALSE;
	
	private $image_URL_trim = NULL;
	
	private $image_URL_segs = NULL;
	
	private $image_file = NULL;
	
	private $image_file_job_check = NULL;
	
	public $image_file_job_JS_source = NULL;
	
	private $image_file_job_JS_file = NULL;
	
	private $image_file_job_exec = NULL;
	
	private $image_file_job_exec_escape_SHELL = NULL;
	
	private $image_file_job_refresh_time = '60*5';
	
	private $image_file_job_BAT_file = NULL;
	
	private $file_exec = array();
	
	private $date = NULL;
	
	private $time = NULL;

	
	
	/*	
	*	Defines and tests the enviroment you are running on
	*	Please note that there was a bug fix, where the iterative returned a ':' value.
	*	You need to make the value less then the $prod variable.
	*/
	function __construct() {
		
		/* 	Please set enviroment 1's instances/nodes
		*	
		*/
		$this->prod1[1] = 'http://www.opensourcematters.org:80'.$this->path;
		$this->prod1[2] = 'http://www.gmail.com:80'.$this->path;

		
		//Set enviroment Prod 2 instances
		$this->prod2[1] = 'http://youtube.com:80'.$this->path;
		$this->prod2[2] = 'http://maps.google.co.za:80'.$this->path;

		
		//Statically count number of prods, figure can be changed from variable that was declared;
		if($this->prod_instance_staging =='prod1')
		{
			$this->prod_instance_count = 2;
			$this->prod_instance_get = $this->prod1;
		}
		
		if($this->prod_instance_staging =='prod2')
		{
			$this->prod_instance_count = 2;
			$this->prod_instance_get = $this->prod2;
		}
				
		$prod_count = $this->prod_instance_count;
		
		//Lets cycle through the different prods to makes sure theyre online 
		$i = NULL;
		
		//Lets check if online is fine
		for($i=1; $i<= $this->prod_instance_count; $i++)
		{
	
					
			$this->instances_strip_host[$i] = parse_url($this->prod_instance_get[$i], PHP_URL_HOST);

			$this->instances_strip_port[$i] = parse_url($this->prod_instance_get[$i], PHP_URL_PORT);


			$this->prod_check[$i] = $this->instances_strip_host[$i].':'.$this->instances_strip_port[$i]; 
			

			/* The follwing loop checks the availability of a resource and pings it to prevent an endless loop and poor code performance
			Please note that it was optimised for 0.3seconds response and anything lower may cause a failed attempt as a fals positive.
			*/
			
			if($fp = @fsockopen($this->prod_check[$i], -1, $errno, $errstr, 1.2))
			{
				$this->prod_instance_messages = 'server '.$this->prod_check[$i].' is up, HELLO!!'."</br>";
				
				fclose($fp);
			}
				
				else {
						$this->prod_instance_messages = $this->prod_check[$i]." failed instance <br>"; 
				} 				
				
			echo $this->prod_instance_messages;		
		}
	}

	
	/* 
		The following function takes images of the servers
		Will add feature to write onto SQL table

	*/
	//Get and purify URL
	PUBLIC function URL_GET($prod)
	{

		//Added $this->images_URL variable resolver	
		$this->images_URL = $prod;

		$download = FALSE;

		if (!isset($this->images_URL)) {
		//Added comments/error	
			throw new Exception( 'URL not set, url_get app exiting!!');
	
			exit();
		}
		$this->image_URL_trim = $this->images_URL;

		$this->image_URL_trim = trim(urldecode($this->image_URL_trim));
		
		if ($this->image_URL_trim == '') {
			throw new Exception( 'URL not set, url_get app exiting!!');
			exit();
		}

		if (!stristr($this->image_URL_trim, 'http://') and !stristr($this->image_URL_trim, 'https://')) {
			$this->image_URL_trim = 'http://' . $this->image_URL_trim;

		}

		$this->image_URL_segs = parse_url($this->image_URL_trim);
		if (!isset($this->image_URL_segs['host'])) {
			throw new Exception( 'URL not set, url_get app exiting!!');
			exit();
			}

		//Begin to compile the image 

		//Bin folder
		$image_file_dir_bin = $_SERVER['DOCUMENT_ROOT'].project_name.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR;
		$image_file_dir_cache = $_SERVER['DOCUMENT_ROOT'].project_name.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
		$image_file_dir_jobs = $_SERVER['DOCUMENT_ROOT'].project_name.DIRECTORY_SEPARATOR.'jobs'.DIRECTORY_SEPARATOR;

		if (!is_dir($image_file_dir_jobs)) {
			mkdir($image_file_dir_jobs);
			file_put_contents($image_file_dir_jobs . 'index.php', '<?php exit(); ?>');

		}
		if (!is_dir($image_file_dir_cache)) {
			mkdir($image_file_dir_cache);
			file_put_contents($image_file_dir_cache . 'index.php', '<?php exit(); ?>');

		}


		$w = 1024;
		$h = 768;


		//Further strip the URL 
		$this->image_URL_trim = strip_tags($this->image_URL_trim);
		$this->image_URL_trim = str_replace(';', '', $this->image_URL_trim);
		$this->image_URL_trim = str_replace('"', '', $this->image_URL_trim);
		$this->image_URL_trim = str_replace('\'', '/', $this->image_URL_trim);
		$this->image_URL_trim = str_replace('<?', '', $this->image_URL_trim);
		$this->image_URL_trim = str_replace('<?', '', $this->image_URL_trim);
		$this->image_URL_trim = str_replace('\077', ' ', $this->image_URL_trim);

		
		//Set the value for the date and removes executable shell from file name
		$this->date = date('Y-m-d');
		
		$this->date = strip_tags($this->date);
		$this->date = str_replace(';', '', $this->date);
		$this->date = str_replace('"', '', $this->date);
		$this->date = str_replace('\'', '/', $this->date);
		$this->date = str_replace('<?', '', $this->date);
		$this->date = str_replace('<?', '', $this->date);
		$this->date = str_replace('\077', ' ', $this->date);
		$this->date = str_replace('-', '_', $this->date);
		
		//Set the value for the time and removes executable shell from file name
		$this->time = date('H:i:s', time() - date('Z'));
		
		$this->time = strip_tags($this->time);
		$this->time = str_replace(';', '', $this->time);
		$this->time = str_replace('"', '', $this->time);
		$this->time = str_replace('\'', '/', $this->time);
		$this->time = str_replace('<?', '', $this->time);
		$this->time = str_replace('<?', '', $this->time);
		$this->time = str_replace('\077', ' ', $this->time);
		$this->time = str_replace(':', '-', $this->time);

		// Create image filename
		//Changed file type to .png
		$this->image_file = $this->image_URL_segs['host'] . '_' . $this->date . '_' . $this->time . '_md5'. md5($this->image_URL_trim). '.png';
		//Folder to put the image in 
		$this->image_file_job_check = $image_file_dir_cache . $this->image_file;

		//Creation of the job file to be parsed to phantomJS 
		$refresh = FALSE;
		if (is_file($this->image_file_job_check)) {
			$filemtime = @filemtime($this->image_file_job_check); // returns FALSE if file does not exist

			if (!$filemtime or (time() - $filemtime >= $this->image_file_job_refresh_time)) {
				$refresh = TRUE;
			//cache time expired.. Now begin child jobs
			}
		}

		//Formulate JS file 
		$this->image_URL_trim = escapeshellcmd($this->image_URL_trim);

		if (!is_file($this->image_file_job_check) or $refresh == TRUE) {
			$this->image_file_job_JS_source = "

			var page = require('webpage').create();

			page.viewportSize = { width: {$w}, height: {$h} };
	
			page.onError = function (msg, trace) {
			console.log(msg);
			trace.forEach(function(item) {
				console.log('  ', item.file, ':', item.line);
			});
			};

			";

			if (isset($clipw) && isset($cliph)) {
				$this->image_file_job_JS_source .= "page.clipRect = { top: 0, left: 0, width: {$clipw}, height: {$cliph} };";
			}

			$this->image_file_job_JS_source .= "

			page.open('{$this->image_URL_trim}', function () {
			page.render('{$this->image_file}');
			phantom.exit();
			});


			";

			//Create JS file for executable 
			$this->image_file_job_JS_file = $image_file_dir_jobs . $this->image_URL_segs['host'] . crc32($this->image_file_job_JS_source) . '.js';
			$this->image_file_job_BAT_file = $image_file_dir_jobs . $this->image_URL_segs['host'] . crc32($this->image_file_job_JS_source) . '.bat';
			file_put_contents($this->image_file_job_JS_file, $this->image_file_job_JS_source);
	
			//Executable command to phantomJS 
			$this->image_file_job_exec = $image_file_dir_bin . 'phantomjs.exe ' . $this->image_file_job_JS_file;
			
			$this->image_file_job_exec = 'start "" '.$this->image_file_job_exec;
						
			$this->image_file_job_exec = escapeshellcmd($this->image_file_job_exec);
			
			file_put_contents($this->image_file_job_BAT_file, $this->image_file_job_exec);
			
			$this->file_exec = exec($this->image_file_job_BAT_file);

			//check and fix!!
			if (is_file($this->image_file)) {
				rename($this->image_file, $this->image_file_job_check);
				}
		};
		
	}
	
		PUBLIC FUNCTION SQL_DLR()
	
	{

		
	}
}	

?>