<?php

/**
 * AssetLoad - A simple CSS & JS asset loader for CodeIgniter
 *
 * @author		Sean Nieuwoudt (http://twitter.com/SeanNieuwoudt)
 * @copyright	Copyright (c) 2013 wixelhq.com
 * @link		http://github.com/Wixel/CI-AssetLoad.git
 * @version     1.0
 */
class AssetLoad
{	
	/**
	 * Simple FIFO mechanism to load the assets
	 *
	 * @param boolean $cache_bust
	 * @param string $manifest_file_name	
	 * @param string $manifest_path	
	 * @return void
	 */
	public function queue($cache_bust = false, $manifest_file_name = 'assets.ini', $manifest_path = 'assets/')
	{
		$manifest_file = $manifest_path.$manifest_file_name;
		$timestamp 	   = null;
		
		if(!defined('ENVIRONMENT')) {
			define('ENVIRONMENT', 'development');
		}
				
		// Let's bust that cache in dev anyways
		if($cache_bust || ENVIRONMENT == 'development') {
			$timestamp = '?'.time();
		}
		
		if(!file_exists($manifest_file)) {
			throw new Exception("The asset loader manifest file could not be found at '$manifest_file'");
		}
		
		// Parse the ini asset manifest
		$manifest = parse_ini_file($manifest_file, true);
		
		// Global/Default assets
		if(isset($manifest['defaults'])) {
			$css = (isset($manifest['defaults']['css']))? $manifest['defaults']['css'] : array();
			$js  = (isset($manifest['defaults']['js'])) ? $manifest['defaults']['js']  : array();
			
			foreach($css as $e) {
				// Check for external or local resource (http:// https:// etc)
				if(strstr($e, '//') !== false) {
					echo self::css($e.$timestamp);
				} else {
					echo self::css("/".$manifest_path.$e.$timestamp);
				}
			}
			
			foreach($js as $e) {
				// Check for external or local resource (http:// https:// etc)
				if(strstr($e, '//') !== false) {
					echo self::script($e.$timestamp);
				} else {
					echo self::script("/".$manifest_path.$e.$timestamp);
				}
			}	
		}		
		
		// Environment specific
		if(isset($manifest[ENVIRONMENT])) {
			$css = (isset($manifest[ENVIRONMENT]['css']))? $manifest[ENVIRONMENT]['css'] : array();
			$js  = (isset($manifest[ENVIRONMENT]['js'])) ? $manifest[ENVIRONMENT]['js']  : array();
			
			foreach($css as $e) {
				// Check for external or local resource (http:// https:// etc)
				if(strstr($e, '//') !== false) {
					echo self::css($e.$timestamp);
				} else {
					echo self::css("/".$manifest_path.$e.$timestamp);
				}
			}
			
			// Check & automatically load a routed CSS file
			if(isset($manifest[ENVIRONMENT]['css_routing'])) {
				echo self::css_route($manifest_path.$manifest[ENVIRONMENT]['css_routing']);
			}			
			
			foreach($js as $e) {
				// Check for external or local resource (http:// https:// etc)
				if(strstr($e, '//') !== false) {
					echo self::script($e.$timestamp);
				} else {
					echo self::script("/".$manifest_path.$e.$timestamp);
				}
			}	
			
			// Check & automatically load a routed JS file
			if(isset($manifest[ENVIRONMENT]['js_routing'])) {
				echo self::script_route($manifest_path.$manifest[ENVIRONMENT]['js_routing']);
			}
		}
		unset($manifest); // clean up
	}
	
	/**
	 * Generate the css link tag
	 *
	 * @param string $path
	 * @return string
	 */
	private static function css($path)
	{
		return '<link rel="stylesheet" href="'.$path.'">'."\n";
	}
	
	/**
	 * Generate the script include tag
	 *
	 * @param string $path
	 * @return string
	 */
	private static function script($path)
	{
		return '<script src="'.$path.'"></script>'."\n";
	}
	
	/**
	 * Generate the automated JS file path according to the current controller
	 *
	 * @param string $route
	 * @return string
	 */
	private static function script_route($path)
	{
		$ci = &get_instance();
		
		return self::script($path.'/'.$ci->router->fetch_class().'.js');
	}
	
	/**
	 * Generate the automated JS file path according to the current controller
	 *
	 * @param string $path
	 * @return string
	 */
	private static function css_route($path)
	{
		$ci = &get_instance();
		
		return self::css($path.'/'.$ci->router->fetch_class().'.css');
	}
	
	/**
	 * Return the class name of the current controller to use as a body class
	 *
	 * @return string
	 */
	public function body_class()
	{
		$ci = &get_instance();
		
		echo $ci->router->fetch_class();
	}
	
} //EOC