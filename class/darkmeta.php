<?php 
/* 
CREATED BY DAVID "CRONOS88" MABASO

@var, @array. url_list, gets full list of resolvable urls that are not yet chopped
@var url_frag_h, creates a list of host from url_list array
@var url_count, creates a number thats used to parse when checking state of url/host. this is retrieved from count(url_list)

 */

class DarkMeta

{
    
    private $url_frag_h = NULL;
    private $url_frag_p = NULL;
    public $url_notice = NULL;
    public $url_heart = NULL;
    public $url_host = NULL;

    function __construct(){
        //These are main enivorment variables

        //set debug on or off
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL); 

    }
    
    PUBLIC function url_heartbeat($url){
        
        //URL validation and cleaning
        if (!isset($url)) {
			throw new Exception( 'No URL defined, url_heartbeat exiting!!');
			exit();
		}

        $URL_trim = $url;
		$URL_trim = trim(urldecode($URL_trim));
        if ($URL_trim == '') {
			throw new Exception( 'No URL defined, url_heartbeat exiting!!');
			exit();
		}

		if (!stristr($URL_trim, 'http://') and !stristr($URL_trim, 'https://')) {
			$URL_trim = 'http://' . $URL_trim;

		}

		$URL_segs = parse_url($URL_trim);
		if (!isset($URL_segs['host'])) {
			throw new Exception( 'No URL defined, url_heartbeat exiting!!');
			exit();
			}

            //Set URL host for fsockopen function
            $this->url_host = $URL_segs['host'];

            /* The following loop checks the availability of a resource and pings it to prevent an endless loop and poor code performance
			Please note that it was optimised for 1.2seconds response and anything lower may cause a failed attempt as a false positive.
			*/
            if($fp = fsockopen($this->url_host, 80, $errno, $errstr, 25)) {
                $this->url_notice = 'Host '.$url.' is up'."</br>";
                fclose($fp);
            }

                else {
                    $this->url_notice = 'Host <b>'.$url.'</b> FAILED'."</br>";
                }
        }

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
		//Folder root
		define("folder_name", "");
		$image_file_dir_bin = $_SERVER['DOCUMENT_ROOT'].folder_name.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR;
		$image_file_dir_cache = $_SERVER['DOCUMENT_ROOT'].folder_name.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
		$image_file_dir_jobs = $_SERVER['DOCUMENT_ROOT'].folder_name.DIRECTORY_SEPARATOR.'jobs'.DIRECTORY_SEPARATOR;
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
			$this->image_file_job_exec = $image_file_dir_bin . 'phantomjs ' . $this->image_file_job_JS_file;
			
			$this->image_file_job_exec = 'start "" '.$this->image_file_job_exec.' & echo off';
						
			$this->image_file_job_exec = escapeshellcmd($this->image_file_job_exec);
			
			file_put_contents($this->image_file_job_BAT_file, $this->image_file_job_exec);
			
			$this->file_exec = exec($this->image_file_job_BAT_file);
			
			if (is_file($this->image_file)) {
				rename($this->image_file, $this->image_file_job_check);
				}
		};
    }

}

?>
