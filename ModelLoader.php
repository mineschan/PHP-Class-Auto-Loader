<? 
/***
Class Name : ModelLoader
Version    : 0.4 beta
Author 	   : MineS Chan
Contact	   : mineschan@gmail.com
Social	   : @mineschan
Description: This is a function to load all the php file with Capital filename or specific prefix in provided directory.

Last Updated: 22/05/2013
***/

class ModelLoader{

	/* The dir location this Modelloader should goto and found with */
	var $dirPath;
	
	/* This array stores the found files */
	var $found;
	
	/* This array stores the actually include/require files*/
	var $loaded;
	
	/* Default method */
	var $method = "include_once";
	
	/* Default prefix = no prefix, look for Capital File names */
	var $prefix = "CAPS";
	
	
    /** Static Function, Scan and load the files in given Directory
      * More information see load() function, this function just pass and call that one
      */	
	public static function LoadDir($dir,$method,$prefix,$debug){
		$class = new ModelLoader();
		return $class->load($dir,$method,$prefix,$debug);
	}
	
	
	
    /** You can set the path and prefix thru this constructor
      * @param $dir The directory,need absolute path.
      * @param $prefix File name prefix, default = "CAPS", but this actually mean File name with Capital letters.
      * @return the class object itself
      */	
	public function __construct($dir,$prefix)
	{
		if($dir != "")
			$this->setDirPath($dir);
		if($prefix != "")
			$this->setPrefix($prefix);
			
		return $this;
	}
	
	
    /**  DirPath setter
      * @param $dir The directory,need absolute path.
      */		
	public function setDirPath($dir){
		$this->dirPath = $dir;
	}
	
    /**  prefix setter
      * @param $prefix File name prefix, default = "CAPS", but this actually mean File name with Capital letters.
      */		
	public function setPrefix($prefix){
		$this->prefix = $prefix;
	}
	
	
    /** Class Function, Scan the files exist in given did
      * @param $dir The directory,need absolute path.
      * @param $prefix File name prefix, default = "CAPS", but this actually mean File name with Capital letters.
      * @param $debug true if you want to debug
      * @return Found files name array
      */		
	public function scanDirectory($dir,$prefix,$debug = false){
		$target_dir = ($dir=="")?$this->dirPath:$dir;
		$prefix = ($prefix=="")?$this->prefix:$prefix;
		$fileFound = array();
					
		if (is_dir($target_dir))
		  $dir_handle = opendir($target_dir);
		
		if (!$dir_handle)
		  return false;
		  
		while($file = readdir($dir_handle)) {
		  if ($file != "." && $file != "..") {
		     $ext = pathinfo($file, PATHINFO_EXTENSION);
		     if($ext != "php")
		     	continue;
		     else{
		     	//no prefix had set, found filename with Capital letter
		     	if($prefix == "CAPS"){
		     		$shouldbe = ucwords($file);
		     		if($file == $shouldbe){
		     			$fileFound[] = $file;
		     		}
		     	}else{
		     		//if prefix set, scan with prefix
		     		if(strpos($file,$prefix)===0)
		     			$fileFound[] = $file;
		     	}
		     }		            
		  }
		}
		closedir($dir_handle);	
				
		if(sizeof($fileFound) > 0){			
			$this->found = $fileFound;
			return $this->found;
		}else{
			return false;
		}
	}
	
    /** Class Function,include/require the file with given method, default "include_one"
      * @param $files Found files array returned by scanDirectory()
      * @param $method include/require method
      * @return Loaded files name array
      */		
	public function loadFiles($files,$method){
		$fileLoad = array();
		
		foreach($files as $file){
			switch($method){
				case "include":
					include($this->dirPath.$file);
					$fileLoad[] = $file;
				break;
				case "include_once":
					include_once($this->dirPath.$file);
					$fileLoad[] = $file;
				break;
				case "require":
					require $this->dirPath.$file;
					$fileLoad[] = $file;
				break;
				case "require_once":
					require_once $this->dirPath.$file;
					$fileLoad[] = $file;
				break;								
			}
		}
		
		$this->loaded = $fileLoad;		
		return $this->loaded;
	}
	
	
    /** Main Function, Scan and load the files in given Directory
      * @param $dir The directory,need absolute path.
      * @param $method include/require method
      * @param $prefix File name prefix, default = "CAPS", but this actually mean File name with Capital letters.
      * @param $debug true if you want to debug
      * @return The loaded files name array
      */		
	public function load($dir,$method,$prefix,$debug){
		$this->dirPath = ($dir=="")?$this->dirPath:$dir;
		
		//scan the directory for existing files first
		$method = ($method=="")?$this->method:$method;
		$files = $this->scanDirectory($this->dirPath,$prefix);
		
		//if we find any,load them
		if($files){
			$load = $this->loadFiles($files,$method);
		}
		
		//return the loaded array if success
		if(sizeof($load) > 0){
			//debug, the message will be show in beginning of html source
			if($debug){ $this->debug();}
					
			$this->loaded = $load;
			return $this->loaded;
		}else{
			return false;
		}		
	}
	
	/* Debug to HTML source */
	public function debug(){
		if(sizeof($this->found)>0){
			$this->debugToHTML("ModelLoader had found these file(s)",$this->found);
		}
		
		if(sizeof($this->loaded)>0){
			$this->debugToHTML("ModelLoader help you loaded these file(s)",$this->loaded);
		}		
	}
	
	/* Generate HTML for debugging */
	private function debugToHTML($message,$files){
		$debug = "<!--$message:\n";
		$count = 1;
		foreach($files as $file){
			$debug .= "$count\t$file\n";
			$count ++;
		}
		$debug .= "-->\n";
		echo $debug;
	}

}

?>
