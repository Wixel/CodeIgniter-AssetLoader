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
	private $ci        = null;
	private $ie_id     = null;
	private $timestamp = null;
	private $load_path = '/';
	private $dns       = array();
	
	public function __construct() 
	{
		$this->ci = &get_instance();
		
		// Set everything up
		if(!defined('ENVIRONMENT')) {
			define('ENVIRONMENT', 'development');
		}
		
		// Load dependencies
		$this->ci->load->helper('url');
		$this->ci->load->library('user_agent');		
		
		// Let's check if we're dealing with IE
		if($this->ci->agent->browser() == 'Internet Explorer') {
			$this->ie_id = 'ie'.$this->ci->agent->version();
		}	
	}
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
		
		if(!file_exists($manifest_file)) {
			throw new Exception("The asset loader manifest file could not be found at '$manifest_file'");
		}
		
		// Set the load path
		$this->load_path = $manifest_path;
		
		// Let's bust that cache in dev anyways
		if($cache_bust || ENVIRONMENT == 'development') {
			$this->timestamp = '?'.time();
		}		
		
		// Parse the ini asset manifest
		$manifest = parse_ini_file($manifest_file, true);
		
		// Global/Default assets
		if(isset($manifest['defaults'])) {
			$this->load_assets($manifest['defaults']);
		}		
		
		// Environment specific
		if(isset($manifest[ENVIRONMENT])) {
			$this->load_assets($manifest[ENVIRONMENT]);
		}
		
		// Let's check for Internet Explorer specific declarations
		if(!empty($this->ie_id) && isset($manifest[$this->ie_id])) {
			$this->load_assets($manifest[$this->ie_id]);
		}
		
		// Let's load the DNS prefetch tags automatically
		$this->dns_prefetch();
		
		unset($manifest); // clean up
	}
	
	/**
	 * Perform the actual asset loading
	 * 
	 * @param array $env
	 * @return void
	 */
	private function load_assets($env)
	{
		$css = (isset($env['css']))? $env['css'] : array();
		$js  = (isset($env['js'])) ? $env['js']  : array();
		
		foreach($css as $e) {
			// Check for external or local resource (http:// https:// etc)
			if(strstr($e, '//') !== false) {
				$this->parse_dns($e);
				echo $this->css($e.$this->timestamp);
			} else {
				echo $this->css("/".$this->load_path.$e.$this->timestamp);
			}
		}
		
		foreach($js as $e) {
			// Check for external or local resource (http:// https:// etc)
			if(strstr($e, '//') !== false) {
				$this->parse_dns($e);
				echo $this->script($e.$this->timestamp);
			} else {
				echo $this->script("/".$this->load_path.$e.$this->timestamp);
			}
		}	
		
		return;	
	}
	
	/**
	 * Generate the css link tag
	 *
	 * @param string $path
	 * @return string
	 */
	private function css($path)
	{
		return '<link rel="stylesheet" href="'.$path.'">'."\n";
	}
	
	/**
	 * Generate the script include tag
	 *
	 * @param string $path
	 * @return string
	 */
	private function script($path)
	{
		return '<script src="'.$path.'"></script>'."\n";
	}
	
	/**
	 * Generate the automated JS file path according to the current controller
	 *
	 * @param string $route
	 * @return string
	 */
	public static function script_route($path)
	{
		return self::script($path.'/'.$this->ci->router->fetch_class().'.js');
	}
	
	/**
	 * Parse and add the DNS paths to the local cache
	 *
	 * @param string $path
	 * @return void
	 */
	private function parse_dns($path)
	{
		$pieces = parse_url($path);
		
		if(isset($pieces['host'])) {
			if(!in_array(strtolower("//".$pieces['host']), $this->dns)) {
				$this->dns[] = "//".strtolower($pieces['host']);
			}
		}
	}
	
	/**
	 * Generate and output the DNS prefetch link tag
	 *
	 * @return void
	 */
	private function dns_prefetch()
	{
		echo '<meta http-equiv="x-dns-prefetch-control" content="on">';
		
		foreach($this->dns as $e) {
			echo '<link rel="dns-prefetch" href="'.$e.'">';
		}
	}
	
	/**
	 * Generate the automated JS file path according to the current controller
	 *
	 * @param string $path
	 * @return string
	 */
	public static function css_route($path)
	{
		return self::css($path.'/'.$this->ci->router->fetch_class().'.css');
	}
	
	/**
	 * Return the class name of the current controller to use as a body class
	 *
	 * @return string
	 */
	public function body_class()
	{
		echo $this->ci->router->fetch_class();
	}
	
} //EOC