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
	 * @param string $manifest_path
	 * @param string $manifest_file_name	
	 * @return void
	 */
	public static function queue($cache_bust = false, $manifest_path = 'assets/', $manifest_file_name = 'assets.ini')
	{
		$manifest_file = $manifest_path.$manifest_file_name;
		$timestamp 	   = null;
		
		if($cache_bust) {
			$timestamp = '?'.time();
		}
		
		if(!defined('ENVIRONMENT')) {
			define('ENVIRONMENT', 'development');
		}
		
		if(!file_exists($manifest_file)) {
			throw new Exception("The asset loader manifest file could not be found at '$manifest_file'");
		}
		
		$manifest = parse_ini_file($manifest_file, true);
		
		// Load global/default assets before others		
		if(isset($manifest['defaults'])) {
			$css = (isset($manifest['defaults']['css']))? $manifest['defaults']['css'] : array();
			$js  = (isset($manifest['defaults']['js'])) ? $manifest['defaults']['js']  : array();
			
			foreach($css as $e) {
				echo self::css_link($manifest_path.$e.$timestamp)."\n";
			}
			
			foreach($js as $e) {
				echo self::script_include($manifest_path.$e.$timestamp)."\n";
			}	
		}		
		
		// Load environment specific assets
		if(isset($manifest[ENVIRONMENT])) {
			$css = (isset($manifest[ENVIRONMENT]['css']))? $manifest[ENVIRONMENT]['css'] : array();
			$js  = (isset($manifest[ENVIRONMENT]['js'])) ? $manifest[ENVIRONMENT]['js']  : array();
			
			foreach($css as $e) {
				echo self::css_link($manifest_path.$e.$timestamp)."\n";
			}
			
			foreach($js as $e) {
				echo self::script_include($manifest_path.$e.$timestamp)."\n";
			}	
		}
		unset($manifest); // clean	
	}
	
	/**
	 * Generate the css link tag
	 *
	 * @param string $path
	 * @return string
	 */
	private static function css_link($path)
	{
		return '<link rel="stylesheet" href="/'.$path.'">';
	}
	
	/**
	 * Generate the script include tag
	 *
	 * @param string $path
	 * @return string
	 */
	private static function script_include($path)
	{
		return '<script src="/'.$path.'"></script>';
	}
	
} //EOC