<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

/*
Config
*/

ob_start();
include('config.php');
ob_end_clean();

/*
Includes
*/

include('class/class.mysql.php');
include('class/class.mainfuncs.php');
require ('' . TWOA_COMPOSER . '/vendor/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;


include('content/' . TWANDO_LANG . '/lang.php');

/*
URL of intall. You can override this if you wish
with a static define in config.php
*/

//if (!defined(BASE_LINK_URL)) {
 //if ($_SERVER['HTTPS']) {$url_check = 'https://';} else {$url_check = 'http://';}
// $url_check .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
 //$filename = array_pop(explode("/",$url_check));
 //$url_check = str_replace($filename,"",$url_check);
 //define('BASE_LINK_URL',$url_check);
//}


/* Grab image url functions so if provided with pic.twitter.com it finds the image or the image url. */

// Defining the basic scraping function
    function scrape_between($data, $start, $end){
        $data = stristr($data, $start); // Stripping all data from before $start
        $data = substr($data, strlen($start));  // Stripping $start
        $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
        $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
        return $data;   // Returning the scraped data from the function
    }

function curl($url) {
	
        // Assigning cURL options to an array
        $options = Array(
            CURLOPT_RETURNTRANSFER => TRUE,  // Setting cURL's option to return the webpage data
            CURLOPT_FOLLOWLOCATION => TRUE,  // Setting cURL to follow 'location' HTTP headers
            CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
            CURLOPT_CONNECTTIMEOUT => 120,   // Setting the amount of time (in seconds) before the request times out
            CURLOPT_TIMEOUT => 120,  // Setting the maximum amount of time for cURL to execute queries
            CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
            CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",  // Setting the useragent
            CURLOPT_URL => $url, // Setting cURL's URL option with the $url variable passed into the function
        );
         
        $ch = curl_init();  // Initialising cURL 
        curl_setopt_array($ch, $options);   // Setting cURL's options using the previously assigned array data in $options
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  //check for issues with the image grab
		if($httpCode == 404) {
			$imageurl = NULL;
			return $imageurl;
		}
		if($httpCode == 403) {
			$imageurl = NULL;
			return $imageurl;
		}
		if($httpCode == 401) {
			$imageurl = NULL;
			return $imageurl;
		}
		if (0 === strpos($url, 'pic.twitter.com')) {  //Does it start with pic.twitter
			$data = scrape_between($data, '<meta  property="og:image" content="', ':large">');  //Grab image url
			$data = file_get_contents($data);
		}
        curl_close($ch);    // Closing cURL 
		$imagefilemd5name = md5 ($url);
		$imagefile = "".UPLOAD_DIR."".$imagefilemd5name.".jpg";
		$myfile = fopen($imagefile, "w") or die("Unable to open file!");
		fwrite($myfile, $data);
		fclose($myfile);
        return $imagefile;   // Returning the data from the function 
    }


/*
Internal defines - you shouldn't need to change these
*/

define('TWANDO_VERSION','0.6');
define('TWITTER_API_LIMIT',15);
define('TWITTER_API_LIST_FW',5000);
define('TWITTER_API_USER_LOOKUP',100);
define('TABLE_ROWS_PER_PAGE',10);
define('TWITTER_TWEET_SEARCH_PP',100);
define('TWITTER_USER_SEARCH_PP',20);

/*
Start
*/

$db = new mySqlCon();
session_start();

?>
