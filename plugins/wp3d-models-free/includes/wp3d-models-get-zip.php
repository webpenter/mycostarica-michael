<?php

require('../../../../wp-load.php');

if ($_GET['mid']) {

	$url = esc_url('https://my.matterport.com/api/player/models/'.$_GET['mid'].'?format=json');
	$content = wp_remote_get($url);
	
	//print_r($content);
	$data = json_decode($content['body'], true);
	//$data = json_decode($content, true);
	$images = $data['images'];

	if ($images) {
	     
	     ignore_user_abort(true);

		$files = array();

		foreach ($images as $image) {
		    $files[] = $image['src'];
		}

		# create new zip opbject
		$zip = new ZipArchive();

		# create a temp file & open it
		$tmp_file = tempnam('.','');
		$zip->open($tmp_file, ZipArchive::CREATE);

		# loop through each file
		foreach($files as $file){

		    # download file
		    $download_file = file_get_contents($file);
		    
		    # clean up the filename
		    $filearr = explode("?", $file, 2);
			$cleanfilename = $filearr[0];

		    # add it to the zip
		    $zip->addFromString(basename($cleanfilename),$download_file);

		}

		# close zip
		$zip->close();

		# send the file to the browser as a download
		header('Content-disposition: attachment; filename=matterport_images_'.$_GET['mid'].'.zip');
		('Content-type: application/zip');
		readfile($tmp_file);
		
		//remove file after download  
          unlink($tmp_file);

	} else { // there was no $images data, bad ID?
		$nogood = true;
	}

// 'mid' is blank - likely an empty form submission
} elseif (!isset($_GET['mid'])) { 
	exit;
}



?>





<?php

	/**
	 * Wrapper function to get MP zip
	 * @param  string $mp_id       ID
	 * @return image SRC           Image source
	 */
	function get_mp_zip ( $mp_id ) {
		
		if ('error' == $mp_id) { // Check for error
		
			return 'mp-error';
			
		} else {
			
			// Get JSON
			$url = esc_url('https://my.matterport.com/api/player/models/'.$mp_id.'?format=json');
			$content = wp_remote_get($url);
			$data = json_decode($content,true);
			$images = $data['images'];
		
			if ($images) {
		
				$files = array();
		
				foreach ($images as $image) {
				    $files[] = $image['src'];
				}
		
				# create new zip opbject
				$zip = new ZipArchive();
		
				# create a temp file & open it
				$tmp_file = tempnam('.','');
				$zip->open($tmp_file, ZipArchive::CREATE);
		
				# loop through each file
				foreach($files as $file){
		
				    # download file
				    $download_file = file_get_contents($file);

				    # clean up the filename
				    $filearr = explode("?", $file, 2);
					$cleanfilename = $filearr[0];
		
				    # add it to the zip
				    $zip->addFromString(basename($cleanfilename),$download_file);				    
		
				}
		
				# close zip
				$zip->close();
		
				# send the file to the browser as a download
				header('Content-disposition: attachment; filename=matterport_images_'.$_GET['mid'].'.zip');
				('Content-type: application/zip');
				readfile($tmp_file);
		
			} else { // there was no $images data, bad ID?
				$nogood = true;
			}
		}		
	}	

?>