<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfSessionJoomla15.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 * I maintain state variables and hidden fields on the page
 * I provide an interface to session variable which prevents
 * session variables from conflicting with other components.
 *
 * I use the concept of a "mode" which is just an array e.g.
 * $_SESSION[$component_name]['default'] or
 * $_SESSION[$component_name]['index']
 *
 * This way I can keep session data for index tabs completely separate.
 * If I can't ind a session variabe in the current mode, I look for it
 * in the 'default' mode. If I can't find it there I just retrun the
 * user-supplied default.
 */

class bfSession extends JSession {

    var $hidden_field_defaults;
    var $state_defaults;
    var $component_name;
    var $session=array();
    var $mode='default'; // This is the tab mode
    var $modelist=array(); // The modes we know about

    /**
     * I store the registry
     */
    var $_registry = null;

    /**
   * The component name is used to keep the state variable independent
   * of the variables in any other component.
   *
   * @param unknown_type $hidden_field_defaults
   */
    function bfSession($component_name='bfSession' ) {

        $this->__construct(  $component_name );
    }


    function __construct( $component_name='bfSession' ) {
    	global $mainframe;

        /* Set up our registry and namespace */
        $this->_registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));

        //  // $this->log("bfSession __construct");
        $this->hidden_field_defaults = $this->_registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.hidden_field_defaults');
        $this->state_defaults = $this->_registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.state_defaults');
        $this->component_name=$component_name;
        $this->mode='default'; // Always reset the mode
        $this->session[$this->mode]=array();
        // Set up default values for the state and hidden fields if
        // they are not already set up.
        foreach($this->state_defaults as $state_variable => $default) {
            if (( $val = $this->get($state_variable,null))==null) {
                // $this->log("__construct: overwriting state $state_variable $default");
                $this->set($state_variable,$default);
            } else {
                // $this->log("__construct: NOT overwriting state $state_variable $val $default as it already has a value");
            }
        }
        foreach($this->hidden_field_defaults as $variable => $default) {
            if (($val = $this->get($variable,null))==null) {
                // $this->log("__construct: overwriting hidden $variable $default");
                $this->set($variable,$default);
            } else {
                // $this->log("__construct: NOT overwriting hidden $variable $val $default");
            }
        }
        // $this->log("constructor called");
    }



    /**
   * Return the state as a list of HTML hidden fields
   * Implement the singleton design pattern. Calling
   * getInstance() to construct the bfSession opject causes only one instance of
   * the state to be active. Just what you need!
   *
   * @return unknown
   */
    function &getInstance ( $component_name='bfSession' ){
        // this implements the 'singleton' design pattern.

        static $instance;
        if (!isset($instance)) {
            $c = __CLASS__;
            $instance = new $c( $component_name );
        } // if

        return $instance;
    } // getInstance

    /**
   * Reset the page state e.g. when a page is loaded from the
   * menu you usually do not want to recall the old state.
   *
   */
    function reset() {
    	return;
         $this->log("--------------- session reset -----------------");
        $this->setMode('default');
        foreach($this->hidden_field_defaults as $state_variable => $default) {
            // $this->log("resetting $state_variable,$default");
            $this->set($state_variable,$default);
        }
        foreach($this->state_defaults as $state_variable => $default) {
            $this->set($state_variable,$default);
        }
        // $this->log("Resetting modes:");
        foreach( $this->modelist as $mode => $value ) {
            // $this->log("  Resetting $mode");
            $this->session[$mode]=array();
        }
    }

    /**
   * Return the state as a list of HTML hidden fields
   *
   * @return unknown
   */
    function get_hidden_field_defaults_html() {
        $retstr='';
        foreach($this->hidden_field_defaults as $state_variable => $ignoreValue) {
            /* box checked and task should always be empty hidden fields */
            if ($state_variable=='boxchecked' OR $state_variable == 'task'){
                $state_value = '';
            } else {
                $state_value = $this->get($state_variable,'');
            }
            $retstr.="<input type=\"hidden\" id=\"$state_variable\" name=\"$state_variable\" value=\"$state_value\">\n";
        }
        return( $retstr );
    }

    /**
   * Return the state as a list of parameters suitable for passing
   * to a javascript function.
   *
   * @return unknown
   */
    function get_parameters_list() {
        $retstr='';
        $first_time_through = 1;
        foreach($this->hidden_field_defaults as $state_variable) {
            $state_value=$this->get($state_variable,'');
            if ($first_time_through) {
                $retstr.="'$state_variable=$state_value'";
                $first_time_through = 0;
            } else {
                $retstr.=",'$state_variable=$state_value'";
            }
        }
        return( $retstr );

    }

    /**
   * Return a sring containing the session. Needs to be updated to
   * cope with index tabs.
   *
   * @return unknown
   */
    function dump() {
        $retstr="State dump: <br>\n";

        foreach($this->getSessionArray() as $variable => $value) {
            $retstr.="   $variable=$value<br>\n";
        }
        return( $retstr );
    }
    /**
   * set the value of a session variable for this component in the required
   * mode.
   *
   * @return old_value
   */
    function set($name,$value,$mode='') {
        $old_value=$this->get($name,'');
        if ($mode == '') $mode=$this->mode;

        if (!isset($this->session[$mode])) $this->session[$mode]=array();

        // $this->log("setting $name to $value in ".$mode." mode");
        $this->session[$mode][$name]=$value;

        parent::set($this->component_name,$this->session);
        return($old_value);
    }
    /**
   * get the value of a session variable for this component
   *
   * @return sesion variable value
   */
    function get($name,$default='',$mode='') {
        $this->session = parent::get($this->component_name);

        if ($mode == '') $mode=$this->mode;

        if (isset($this->session[$mode][$name])) {
            // $this->log("get: Hit ".$mode." $name = ".$this->session[$mode][$name]);
            return($this->session[$mode][$name]);
        } else if (isset($this->session['default'][$name])) {
            // $this->log("get: Hit (default) $name = ".$this->session['default'][$name]);
            return($this->session['default'][$name]);
        } else {
            // $this->log("get: Miss $name defaulting to '$default'");
            return $default;
        }
    }

    /**
   * get the session array for this component
   * giving priority to modal values from the tab/index
   *
   * @return sesion array
   */
    function getSessionArray() {
        //echo "bfSession component ".$this->component_name."<br>\n";
        $componentsession=parent::get($this->component_name);

        if ($this->mode == 'default') {
            return($componentsession['default']);
        } else {
            $sessionarray=array_merge($componentsession['default'],$componentsession[$this->mode]);
        }
        return($sessionarray);
    }

    /**
     * Set the tab mode for index pages. This allows
     * Different tabs to maintain different states.
     *
     * @param unknown_type $msg
     */
    function setMode( $mode ) {
        // $this->log("---------- setting mode to $mode ---------");
        $this->mode=$mode;
        $this->modelist[$mode]=1;
        if (!isset($this->session[$this->mode]) || !is_array($this->session[$this->mode])) $this->session[$this->mode]=array();
    }

    /**
     * Return the index tab mode
     *
     * @return unknown
     */
    function getMode() {
        return($this->mode);
    }

    /**
     * Set the tab index returnto location
     *
     * @param location $target
     */
    function returnto( $target ) {
        $this->set('returnto',$target,'default');
    }

    /**
     * log for bfSession
     *
     * @param unknown_type $msg
     */
    function log( $msg ) {
        $log = &bfLog::getInstance();
        $log->log(__CLASS__.":$msg");
    }

}
?>