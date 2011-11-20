<?php
class CaptchaController extends Object {
	
	public function defaultCall(){
		session_start();
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		//$this->create_image(); 		
		$im = @imagecreatefromjpeg($this->getCaptchaImgPath()); 
		$rand = $this->generateRandom(3);
		$_SESSION['captcha'] = $rand;
		ImageString($im, 5, 2, 2, $rand[0]." ".$rand[1]." ".$rand[2]." ", ImageColorAllocate ($im, 0, 0, 0));
		$rand = $this->generateRandom(3);
		ImageString($im, 5, 2, 2, " ".$rand[0]." ".$rand[1]." ".$rand[2], ImageColorAllocate ($im, 255, 0, 0));
		header ('Content-type: image/jpeg');
		ImageJpeg($im,NULL,100);
		ImageDestroy($im);
	}
	
	private function generateRandom($length=6)
	{
		$_rand_src = array(
			array(48,57) //digits
			, array(97,122) //lowercase chars
	//		, array(65,90) //uppercase chars
		);
		srand ((double) microtime() * 1000000);
		$random_string = "";
		for($i=0;$i<$length;$i++){
			$i1=rand(0,sizeof($_rand_src)-1);
			$random_string .= chr(rand($_rand_src[$i1][0],$_rand_src[$i1][1]));
		}
		return $random_string;
	}
	
	private function getCaptchaImgPath($module='captcha', $name='captcha'){		
	    if( !$module )
	        $module = $this->getModuleName();
	    else
	        $module = strtolower( $module );	    
		return locale($module.DIRECTORY_SEPARATOR.$name.'.jpg');
	}
	
	private function create_image() 
	{ 
	    //Let's generate a totally random string using md5 
	    $md5_hash = md5(rand(0,999)); 
	    //We don't need a 32 character long string so we trim it down to 5 
	    $security_code = substr($md5_hash, 15, 5); 
	
	    //Set the session to store the security code
	    $_SESSION["captcha"] = $security_code;
	
	    //Set the image width and height 
	    $width = 100; 
	    $height = 20;  
	
	    //Create the image resource 
	    $image = ImageCreate($width, $height);  
	
	    //We are making three colors, white, black and gray 
	    $white = ImageColorAllocate($image, 255, 255, 255); 
	    $black = ImageColorAllocate($image, 0, 0, 0); 
	    $grey = ImageColorAllocate($image, 204, 204, 204); 
	
	    //Make the background black 
	    ImageFill($image, 0, 0, $black); 
	
	    //Add randomly generated string in white to the image
	    ImageString($image, 3, 30, 3, $security_code, $white); 
	
	    //Throw in some lines to make it a little bit harder for any bots to break 
	    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
	    imageline($image, 0, $height/2, $width, $height/2, $grey); 
	    imageline($image, $width/2, 0, $width/2, $height, $grey); 
	 
	    //Tell the browser what kind of file is come in 
	    header("Content-Type: image/jpeg"); 
	
	    //Output the newly created image in jpeg format 
	    ImageJpeg($image); 
	    
	    //Free up resources
	    ImageDestroy($image); 
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function display()
	{
		session_start();
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		//$this->create_image(); 		
		$im = @imagecreatefromjpeg($this->getCaptchaImgPath()); 
		$rand = $this->generateRandom(3);
		$_SESSION['captcha'] = $rand;
		ImageString($im, 5, 2, 2, $rand[0]." ".$rand[1]." ".$rand[2]." ", ImageColorAllocate ($im, 0, 0, 0));
		$rand = $this->generateRandom(3);
		ImageString($im, 5, 2, 2, " ".$rand[0]." ".$rand[1]." ".$rand[2], ImageColorAllocate ($im, 255, 0, 0));
		header ('Content-type: image/jpeg');
		ImageJpeg($im,NULL,100);
		ImageDestroy($im);
		exit(0);
	}			
}
?>