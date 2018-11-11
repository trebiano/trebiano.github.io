<?php
defined( '_JEXEC' ) or die( 'Restricted access to bfCache' );
/**
 * @version $Id: bfCache.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
bfLoad('bfConfig');
class bfCache {
	var $CACHE_SERIAL_HEADER = "<?php\n/*";
	var $CACHE_SERIAL_FOOTER = "*/\n?>";
	var $cache_dir;
	var $cache_enabled = false;
	var $expiration_time = 9000;
	var $flock_filename = 'bfCache.lock';
	var $mutex;
	var $cache = array ();
	var $dirty_objects = array ();
	var $non_existant_objects = array ();
	var $global_groups = array ('sql');
	var $blog_id;
	var $cold_cache_hits = 0;
	var $cold_cache_hits_array = array();
	var $warm_cache_hits = 0;
	var $warm_cache_hits_array = array();
	var $cache_misses = 0;
	var $secret = '';

	function &getInstance ($component = null) {
		if ($component===null){
			global $mainframe;
			$component = $mainframe->get('component','framework');
		}

		static $instance;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (!isset ($instances[$component])) {
			$c = __CLASS__;
			$instances[$component] = new $c($component);
			$config =& bfConfig::getInstance($component);

			if (@$config->bfCachingEnabled =="1"){
				$instances[$component]->cache_enabled = true;
			}

			$instance->expiration_time = @$config->cachetime;
		}
		/*
		if (!isset($instance)) {
		$c = __CLASS__;
		$instance = new $c();
		$config =& bfConfig::getInstance($component);

		if (@$config->bfCachingEnabled =="1"){
		$instance->cache_enabled = true;
		}

		$instance->expiration_time = @$config->cachetime;
		}*/
		return $instances[$component];
		//		return $instance;
	}

	function bfCache() {

		return $this->__construct();
	}

	function __construct() {
		global $blog_id;

		register_shutdown_function(array(&$this, "__destruct"));

		// Disable the persistent cache if safe_mode is on.
		if ( ini_get('safe_mode') )	return;

		// Using the correct separator eliminates some cache flush errors on Windows

		$this->cache_dir = bfCompat::getAbsolutePath() .DIRECTORY_SEPARATOR.bfCompat::mambotsfoldername().DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'blueflame'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;

		if (!is_writable($this->cache_dir) || !is_dir($this->cache_dir)) {
			$this->cache_enabled = false;
			echo 'bfCache folder unwriteable';
		}

		if ( !is_dir($this->cache_dir)) {
			$this->cache_enabled = false;
			echo 'bfCache folder not a directory';
		}

		if (defined('CACHE_EXPIRATION_TIME')) {
			$this->expiration_time = CACHE_EXPIRATION_TIME;
		}

		global $mainframe;
		$this->secret = $mainframe->getCfg('secret');
		$this->blog_id = $this->hash($mainframe->getCfg('secret').$mainframe->getCfg('absolute_path'));

	}


	function acquire_lock() {
		// Acquire a write lock.
		$this->mutex = @fopen($this->cache_dir.$this->flock_filename, 'w');
		if ( false == $this->mutex)
		return false;
		flock($this->mutex, LOCK_EX);
		return true;
	}

	function add($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
		$group = 'default';

		if (false !== $this->get($id, $group, false))
		return false;

		return $this->set($id, $data, $group, $expire);
	}

	function delete($id, $group = 'default', $force = false) {
		if (empty ($group))
		$group = 'default';

		if (!$force && false === $this->get($id, $group, false))
		return false;

		if (isset($this->cache[$group][$id])) {
			unset ( $this->cache[$group][$id] );
		}
		$this->non_existant_objects[$group][$id] = true;
		$this->dirty_objects[$group][] = $id;
		return true;
	}

	function flush() {
		if ( !$this->cache_enabled )
		return true;

		if ( ! $this->acquire_lock() )
		return false;

		$this->rm_cache_dir();
		$this->cache = array ();
		$this->dirty_objects = array ();
		$this->non_existant_objects = array ();

		$this->release_lock();

		return true;
	}

	function get($id, $group = 'default', $count_hits = true) {
		if ($this->cache_enabled==false ) return false;
		if (empty ($group))
		$group = 'default';

		if (isset ($this->cache[$group][$id])) {
			if ($count_hits)
			$this->warm_cache_hits += 1;
			$this->warm_cache_hits_array[] = array($id, $group);
			return $this->cache[$group][$id];
		}

		if (isset ($this->non_existant_objects[$group][$id])){

			return false;
		}

		//  If caching is not enabled, we have to fall back to pulling from the DB.
		//		if (!$this->cache_enabled) {
		//			if (!isset ($this->cache[$group]))
		//			$this->load_group_from_db($group);
		//
		//			if (isset ($this->cache[$group][$id])) {
		//				$this->cold_cache_hits += 1;
		//
		//				return $this->cache[$group][$id];
		//			}
		//
		//			$this->non_existant_objects[$group][$id] = true;
		//			$this->cache_misses += 1;
		//			return false;
		//		}

		$cache_file = $this->cache_dir.$this->get_group_dir($group)."/".$this->hash($id).'.php';
		if (!file_exists($cache_file)) {
			$this->non_existant_objects[$group][$id] = true;
			$this->cache_misses += 1;
			return false;
		}

		// If the object has expired, remove it from the cache and return false to force
		// a refresh.
		$now = time();
		if ((filemtime($cache_file) + $this->expiration_time) <= $now) {
			$this->cache_misses += 1;
			$this->delete($id, $group, true);

			return false;
		}

		$this->cache[$group][$id] = unserialize(
		base64_decode(
		substr( file_get_contents($cache_file), strlen($this->CACHE_SERIAL_HEADER), -strlen($this->CACHE_SERIAL_FOOTER) )
		)
		);
		if (false === $this->cache[$group][$id]){

			$this->cache[$group][$id] = '';
		}

		$this->cold_cache_hits += 1;
		$this->cold_cache_hits_array[] = array($id, $group);

		return $this->cache[$group][$id];
	}

	function get_group_dir($group) {
		if (false !== array_search($group, $this->global_groups))
		return $group;

		return "{$this->blog_id}/$group";
	}

	function hash($data) {
		if ( function_exists('hash_hmac') ) {
			return hash_hmac('md5', $data, $this->secret);
		} else {
			return md5($data . $this->secret);
		}
	}

	function load_group_from_db($group) {
		global $wpdb;

		if ('category' == $group) {
			$this->cache['category'] = array ();
			if ($dogs = $wpdb->get_results("SELECT * FROM $wpdb->categories")) {
				foreach ($dogs as $catt)
				$this->cache['category'][$catt->cat_ID] = $catt;
			}
		} else
		if ('options' == $group) {
			$wpdb->hide_errors();
			if (!$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'")) {
				$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options");
			}
			$wpdb->show_errors();

			if ( ! $options )
			return;

			foreach ($options as $option) {
				$this->cache['options'][$option->option_name] = $option->option_value;
			}
		}
	}

	function make_group_dir($group, $perms) {
		$group_dir = $this->get_group_dir($group);
		$make_dir = '';
		foreach (split('/', $group_dir) as $subdir) {
			$make_dir .= "$subdir/";
			if (!file_exists($this->cache_dir.$make_dir)) {
				if (! @ mkdir($this->cache_dir.$make_dir))
				break;
				@ chmod($this->cache_dir.$make_dir, $perms);
			}

			if (!file_exists($this->cache_dir.$make_dir."index.php")) {
				$file_perms = $perms & 0000666;
				@ touch($this->cache_dir.$make_dir."index.php");
				@ chmod($this->cache_dir.$make_dir."index.php", $file_perms);
			}
		}

		return $this->cache_dir."$group_dir/";
	}

	function rm_cache_dir() {
		$dir = $this->cache_dir;
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);
		$top_dir = $dir;
		$stack = array($dir);
		$index = 0;

		while ($index < count($stack)) {
			# Get indexed directory from stack
			$dir = $stack[$index];

			$dh = @ opendir($dir);
			if (!$dh)
			return false;

			while (($file = @ readdir($dh)) !== false) {
				if ($file == '.' or $file == '..')
				continue;

				if (@ is_dir($dir . DIRECTORY_SEPARATOR . $file))
				$stack[] = $dir . DIRECTORY_SEPARATOR . $file;
				else if (@ is_file($dir . DIRECTORY_SEPARATOR . $file))
				@ unlink($dir . DIRECTORY_SEPARATOR . $file);
			}

			$index++;
		}

		$stack = array_reverse($stack);  // Last added dirs are deepest
		foreach($stack as $dir) {
			if ( $dir != $top_dir)
			@ rmdir($dir);
		}

	}

	function release_lock() {
		// Release write lock.
		flock($this->mutex, LOCK_UN);
		fclose($this->mutex);
	}

	function replace($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
		$group = 'default';

		if (false === $this->get($id, $group, false))
		return false;

		return $this->set($id, $data, $group, $expire);
	}

	function set($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
		$group = 'default';

		if (NULL == $data)
		$data = '';

		$this->cache[$group][$id] = $data;
		if (@$this->non_existant_objects[$group][$id]){
			unset($this->non_existant_objects[$group][$id]);
		}
		$this->dirty_objects[$group][] = $id;

		return true;
	}

	function save() {
		//$this->stats();

		if (!$this->cache_enabled)
		return true;

		if (empty ($this->dirty_objects))
		return true;

		// Give the new dirs the same perms as wp-content.
		$stat = stat($this->cache_dir);
		$dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
		$file_perms = $dir_perms & 0000666; // Remove execute bits for files.

		// Make the base cache dir.
		if (!file_exists($this->cache_dir)) {
			if (! @ mkdir($this->cache_dir))
			return false;
			@ chmod($this->cache_dir, $dir_perms);
		}

		if (!file_exists($this->cache_dir."index.php")) {
			@ touch($this->cache_dir."index.php");
			@ chmod($this->cache_dir."index.php", $file_perms);
		}

		if ( ! $this->acquire_lock() )
		return false;

		// Loop over dirty objects and save them.
		$errors = 0;
		foreach ($this->dirty_objects as $group => $ids) {
			$group_dir = $this->make_group_dir($group, $dir_perms);

			$ids = array_unique($ids);
			foreach ($ids as $id) {
				$cache_file = $group_dir.$this->hash($id).'.php';

				// Remove the cache file if the key is not set.
				if (!isset ($this->cache[$group][$id])) {
					if (file_exists($cache_file))
					@ unlink($cache_file);
					continue;
				}

				$temp_file = tempnam($group_dir, 'tmp');
				$serial = $this->CACHE_SERIAL_HEADER.base64_encode(serialize($this->cache[$group][$id])).$this->CACHE_SERIAL_FOOTER;
				$fd = @fopen($temp_file, 'w');
				if ( false === $fd ) {
					$errors++;
					continue;
				}
				fputs($fd, $serial);
				fclose($fd);
				if (!@ rename($temp_file, $cache_file)) {
					if (@ copy($temp_file, $cache_file))
					@ unlink($temp_file);
					else
					$errors++;
				}
				@ chmod($cache_file, $file_perms);
			}
		}

		$this->dirty_objects = array();

		$this->release_lock();

		if ( $errors )
		return false;

		return true;
	}

	function stats($extended=false) {
		echo "<p>";
		echo "<strong>Cold Cache Hits:</strong> {$this->cold_cache_hits}<br/>";
		# TRANSLATE?
		print_R($this->cold_cache_hits_array);
		echo "<strong>Warm Cache Hits:</strong> {$this->warm_cache_hits}<br/>";
		print_R($this->warm_cache_hits_array);
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br/>";
		echo "</p>";

		if ($extended==true){
			foreach ($this->cache as $group => $cache) {
				echo "<p>";
				echo "<strong>Group:</strong> $group<br/>";
				echo "<strong>Cache:</strong>";
				echo "<pre>";
				print_r($cache);
				echo "</pre>";
				if (isset ($this->dirty_objects[$group])) {
					echo "<strong>Dirty Objects:</strong>";
					echo "<pre>";
					print_r(array_unique($this->dirty_objects[$group]));
					echo "</pre>";
					echo "</p>";
				}
			}
		}
	}



	function __destruct() {
		$this->save();
		return true;
	}


	/**
	 * Public function
	 *
	 */
	function api_add($key, $data, $flag = '', $expire = 0) {
		$data = unserialize(serialize($data));

		return $this->add($key, $data, $flag, $expire);
	}

	function api_close() {
		if ( ! isset($this) )
		return;
		return $this->save();
	}

	function api_delete($id, $flag = '') {
		return $this->delete($id, $flag);
	}

	function api_flush() {
		return $this->flush();
	}

	function api_get($id, $flag = '') {
		return $this->get($id, $flag);
	}

	function api_init() {
		$GLOBALS['bfCache'] =& new bfCache();
	}

	function api_replace($key, $data, $flag = '', $expire = 0) {
		$data = unserialize(serialize($data));

		return $this->replace($key, $data, $flag, $expire);
	}

	function api_set($key, $data, $flag = '', $expire = 0) {
		$data = unserialize(serialize($data));

		return $this->set($key, $data, $flag, $expire);
	}
}
?>
