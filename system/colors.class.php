<?php
	class Colors {
		private static $foreground_colors = array();
	 	private static $background_colors = array();
	 
	 	public static function loadColors(){
	 		Colors::$foreground_colors['black'] = '0;30';
		 	Colors::$foreground_colors['dark_gray'] = '1;30';	
		 	Colors::$foreground_colors['blue'] = '0;34';	
		 	Colors::$foreground_colors['light_blue'] = '1;34';	
		 	Colors::$foreground_colors['green'] = '0;32';	
		 	Colors::$foreground_colors['light_green'] = '1;32';	
		 	Colors::$foreground_colors['cyan'] = '0;36';	
		 	Colors::$foreground_colors['light_cyan'] = '1;36';	
		 	Colors::$foreground_colors['red'] = '0;31';	
		 	Colors::$foreground_colors['light_red'] = '1;31';	
		 	Colors::$foreground_colors['purple'] = '0;35';	
		 	Colors::$foreground_colors['light_purple'] = '1;35';	
		 	Colors::$foreground_colors['brown'] = '0;33';	
		 	Colors::$foreground_colors['yellow'] = '1;33';	
		 	Colors::$foreground_colors['light_gray'] = '0;37';	
		 	Colors::$foreground_colors['white'] = '1;37';	
		 	Colors::$background_colors['black'] = '40';	
		 	Colors::$background_colors['red'] = '41';	
		 	Colors::$background_colors['green'] = '42';	
		 	Colors::$background_colors['yellow'] = '43';	
		 	Colors::$background_colors['blue'] = '44';	
		 	Colors::$background_colors['magenta'] = '45';	
		 	Colors::$background_colors['cyan'] = '46';	
		 	Colors::$background_colors['light_gray'] = '47';
	 	}
	 
	 	// Returns colored string
	 	public static function cs($string, $foreground_color = null, $background_color = null) {
	 		if(Synful::$config['system']['color']){
		 		$colored_string = "";
		 
		 		// Check if given foreground color found
		 		if (isset(Colors::$foreground_colors[$foreground_color])) {
		 			$colored_string .= "\033[" . Colors::$foreground_colors[$foreground_color] . "m";
		 		}	

		 		// Check if given background color found
		 		if (isset(Colors::$background_colors[$background_color])) {
		 			$colored_string .= "\033[" . Colors::$background_colors[$background_color] . "m";
		 		}
		 
		 		// Add string and end coloring
		 		$colored_string .=  $string . "\033[0m";
	 		}else{
	 			$colored_string = $string;
	 		}

	 		return $colored_string;
	 	}
	 
	 	// Returns all foreground color names
	 	public function getForegroundColors() {
	 		return array_keys(Colors::$foreground_colors);
	 	}
	 
	 	// Returns all background color names
	 	public function getBackgroundColors() {
	 		return array_keys(Colors::$background_colors);
	 	}
	 }
 ?>