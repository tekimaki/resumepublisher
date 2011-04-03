<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/* vim: :set fdm=marker : */
/**
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 * Copyright (c) 2010 will james will@tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id: $
 * @package pkgmkr
 * @subpackage functions
 */

/**
 * define shorthand directory separator constant
 */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

if (!defined('RESPUB_ROOT_PATH'))
	define( 'RESPUB_ROOT_PATH', empty( $_SERVER['VHOST_DIR'] ) ? dirname( __FILE__ ).'/' : $_SERVER['VHOST_DIR'].'/' );

if (!defined('RESPUB_EXTERNALS_PATH'))
	define("RESPUB_EXTERNALS_PATH", RESPUB_ROOT_PATH . 'externals' . DS);

if (!defined('RESPUB_TEMP_PATH'))
	define("RESPUB_TEMP_PATH", RESPUB_ROOT_PATH . 'temp' . DS);

if (!defined('RESPUB_CONFIG_PATH'))
	define("RESPUB_CONFIG_PATH", RESPUB_ROOT_PATH . 'config' . DS);

require_once( RESPUB_ROOT_PATH.'ResumeSmarty.php' );

require_once( RESPUB_EXTERNALS_PATH."spyc/spyc.php");


class ResumePublisher{

	private $_verbose = TRUE;

	private $_validate = TRUE;

	private $_publish_path;

	private $_smarty;

	private $_output_format;

	public function __construct()
	{
		// Avoid anoying errors about timezone
		ini_set('date.timezone', 'GMT');

		// Where is the root?
		// $script_path = $_SERVER['PWD'] .'/'. $_SERVER["SCRIPT_FILENAME"];
		// $root = preg_replace('|resumepublisher/generate\.php|', '', preg_replace('|/./|', '/', $script_path));

		// some convenient programming tools
		/*
		require_once( KERNEL_PKG_PATH.'kernel_lib.php' );
		// some convenient debugging tools
		require_once( KERNEL_PKG_PATH.'bit_error_inc.php' );
		*/

		// Initialize Smarty
		$this->setSmarty( new ResumeSmarty() );
	}

	public function generate( $argv ){
		$spec = $this->check_args( $argv );

		if( !is_array( $spec ) ){
			$this->error("Nothing to render - please check your yaml specification file.");
		}else{
			$this->render( $spec );
		}
	}

	private function render( $config ){
		$this->message("Generating resume.");

		// Convenience
		$path = $this->getPublishPath();

		// Assign the configuration to context
		$this->_smarty->assign('config', $config);

		// Change directory to generate the files 
		chdir( $path );

		$files = array( 'resume.'.$this->_output_format );

		$this->renderFiles($config, $path, $files);
	}

	private function validatePublishPath( $path )
	{
		// Does the directory exist
		if (!is_dir($path)) {
			// echo " ".$path."\n";
			if( !mkdir( $path, 0755, TRUE ) ){
				$this->error("Could not create publish directory!");
			}
		}
	}

	private function renderFiles( $config, $dir, $files )
	{
		foreach( $files as $file )
		{
			$template = $file.".tpl";
			$this->renderFile($config, $dir, $file, $template);
		}
	}

	private function renderFile( $config, $dir, $file, $template )
	{
		echo "-> Rendering $file\n";
		$filename = $dir."/".$file;
		$this->message(" ".$filename);

		// Get the contents of the file from smarty
		$content = $this->_smarty->fetch($template);
		if (!empty($content)) {
			if (!$handle = fopen($filename, 'w+')) {
				$this->error("Cannot open file ($filename)");
			}

			// Write $content to our opened file.
			if (fwrite($handle, $content) === FALSE) {
				$this->error("Cannot write to file ($filename)");
			}

			fclose($handle);
		} else {
			$this->error("Error generating file: $filename");
		}
	}

	/**
	 * @see usage
	 */
	private function check_args($argv)
	{
		if (count($argv) ==1 || count($argv)> 4 )
		{
			$this->usage($argv);
		}

		// path to yaml
		$files = array($argv[1], RESPUB_CONFIG_PATH.'source/'.$argv[1].'.yaml' );

		// publish path
		$this->setPublishPath( $argv[2] );

		// output format
		$this->setOutputFormat( (!empty( $argv[3] )?$argv[3]:'html') );

		foreach ($files as $file) {
			if (is_file($file) && is_readable($file))
			{
				echo "Loading $file\n";
				$yaml = Spyc::YAMLLoad($file);
				return $yaml;
			}
		}
		foreach ($files as $file)
		{
			$this->error("Not a readable file: " .$file, false);
		}
		die;
	}

	// required arguments
	// argv[1] : path to resume.yaml 
	// argv[2] : path to target directory where resume output files should be published
	private function usage($argv) {
		echo "Usage: ".$argv[0]." <resume_yaml_path> <output_dir_path> <format>\n";
		die;
	}

	private function error($message, $fatal=TRUE) {
		echo $message;
		echo "\n";
		if ($fatal)
			die;
	}

	private function message($message) {
		if ($this->_verbose)
		{
			echo $message;
			echo "\n";
		}
	}

	/**
	 * lintFile
	 */
	public function lintFile($filename) {
		$this->message(" ... verifying ...");

		exec("php -l $filename", $output, $ret);
		if ($ret != 0) {
			$this->error("ERROR: The generated file: $filename is invalid.", FALSE);
			$this->error($output, FALSE);
		}
	}

	private function getPublishPath()
	{
		return $this->_publish_path;
	}

	private function setPublishPath( $path )
	{
		$this->_publish_path = $path;
	}

	private function setSmarty( ResumeSmarty $smarty )
	{
		$this->_smarty = $smarty;
	}

	private function setOutputFormat( $format )
	{
		$this->_output_format = $format;
	}

}
