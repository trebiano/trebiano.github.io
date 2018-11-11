<?php
//define( '_JEXEC' , '1');
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfValidate.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 * I do form validation using the methods in bfVerify
 * I'm not completely sure this is philosophically right.
 * Some of it feels like it should just extend the bfValidation model
 * but has other methods like validate() that don't belong there.
 */
bfLoad('bfVerify');

/**
 * Enter description here...
 *
 */
class bfValidate {
	/**
	 * @var array of arrays
	 * the format is 'validation method name' => array('number of parameters','text explanation')
	 *
	 * For the text explanation i've left out the "Check that the input is a " part of the string
	 */
	var $valdations=array();
	/**
	 * @var an array of error messags
	 * In the format:
	 * array( number of parameters, message, error_message )
	 */
	var $errors=array();

	/**
	 * @var a bfLog instance
	 */
	var $log;

	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfValidate() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		$this->errors=array();
		$this->validations=array(
		'isinteger' => array(		1,bfText::_('whole number (integer)'),					bfText::_('not a whole number')),
		'isfloat' => array(			1,bfText::_('floating point number'),					bfText::_('not a floating point number')),
		'isnumeric'=>array(			1,bfText::_('Check that the input is numeric'),			bfText::_('not numeric')),
		'alphanumeric'=>array(		1,bfText::_('Check that the input is alphanumeric'),		bfText::_('not alphanumeric')),
		'isemailaddress'=>array(	1,bfText::_('valid email address'),						bfText::_('not a valid email address')),
		'isemailaddressbydns'=>array(1,bfText::_('valid email address by contacting the target mail server'),bfText::_('not a valid email address')),
		'isblank'=>array(			1,bfText::_('Check that the input is empty'),			bfText::_('not empty')),
		'isnotblank'=>array(		1,bfText::_('Check that the input is not empty'),		bfText::_('empty')),
		'ipaddress'=>array(			1,bfText::_('valid IP (v4) address'),					bfText::_('not an IP address')),
		'UKNINumber'=>array(		1,bfText::_('valid UK National Insurance number'),		bfText::_('not a UK national insurance number')),
		'SSN'=>array(				1,bfText::_('valid US Social Security number'),			bfText::_('not a US Social Security Number')),
		'CreditCardNumber'=>array(	1,bfText::_('valid credit card number'),					bfText::_('not a valid credit card number')),
		'CreditCardType'=>array(	1,bfText::_('credit card type'),							bfText::_('not a valid credit card type')),
		'uszip'=>array(				1,bfText::_('valid US ZIP code'),						bfText::_('not a valid US ZIP code')),
		'dateformat'=>array(		1,bfText::_('valid date dd/mm/yyyy or mm/yy/dddd or yyyy/mm/dd'),bfText::_('not a valid date format')),
		'ukpostcode'=>array(		1,bfText::_('valid UK post code'),						bfText::_('not a valid UK post code')),
		'URL'=>array(				1,bfText::_('valid URL'),								bfText::_('not a valid URL')),
		'vatnumber'=>array(			1,bfText::_('VAT Number'),								bfText::_('not a valid VAT number')),
		'equalto'=>array(			2,bfText::_('equal to something else'),					bfText::_('')),
		'numberequals'=>array(		2,bfText::_('equal to a particular number'),				bfText::_('')),
		'numberlessthan'=>array(	2,bfText::_('less than a particular number'),			bfText::_('')),
		'numbergreaterthan'=>array(	2,bfText::_('greater than a particular number'),			bfText::_('')),
		'range'=>array(				3,bfText::_('in a particular range'),					bfText::_('')),
		'regex'=>array(				2,bfText::_('matches a given regular expression'),		bfText::_('')),
		'stringlength'=>array(		2,bfText::_('input string is a given size'),				bfText::_('not the right size')),
		'stringlengthgreaterthan'=>array(2,bfText::_('input string is greater than a given size'),bfText::_('greater than the required maximum size')),
		'stringlengthlessthan'=>array(2,bfText::_('input string is less than a given size'),	bfText::_('less than the required minimum size')),
		'eitheror'=>array(			3,bfText::_('exactly one of two inputs are set'),		bfText::_('two values set where only one was expected'))
		);

		$this->log =& bfLog::getInstance();
	}

	/**
	 * I validate an element's value
	 * If value is unset I look in the request for it.
	 * @param integer $element_id
	 * @param undefined $value
	 */
	function validatebyelement( $form_element_id, $value='' ) {

		$verify=new bfVerify();
		$element = new Form_Element();
		$element->load_element_validations($form_element_id);
		$ret=true;

		// If value is unset look in the request for it.
		if ($value=='') {
			$elementname=$element->elementname;
			$value=bfRequest::getVar($elementname,'');
		}

		/* check this element has valid and published rules */
		if (sizeof($element->element_validations) == 0) $this->log("Element $form_element_id has no validations");

		foreach( $element->element_validations as $ev ) {
			// print_r($ev);
			$verify_method=$ev->type;

			/* check this method exists */
			if (!method_exists($verify,$verify_method)) {
				bfError::raiseError(500,"Attempt to validate a form using the $verify_method method on bfVerify. This method does not exist");
				return false;
			}

			/* do the validation */
			$ret = $verify->$verify_method($value,$ev->param1,$ev->param2,$ev->param3);

			/* We failed validation */
			if (!$ret) {

				/* check for custom error message */
				if (strlen($ev->emsg) > 1){
					/* return custom error message */
					$this->errors[]=array($form_element_id,$ev->emsg,$verify_method,$value,$ret);
				}else{
					/* return standard error message */
					$this->errors[]= array($form_element_id,bfText::_('This element failed validation because the value is ') . $this->validations[$verify_method][2],$verify_method,$value,$ret);
				}

				$ret = false;
			} else {
				/* We passed validation */
				$ret = true;
			}
		}

//		if ($ret) $this->log("Element Validation success");
//		else $this->log("Element Validation FAILED");

		return $ret;
	}

	/**
	 * I validate a whole form against the request. Once complete
	 * $this->errors contains arrays of error messages and element ids
	 * so sizeof($this->errors) == 0 => All is fine.
	 */
	function validatebyform( $form_id ) {
		$this->log("validatebyform $form_id");
		$this->errors=array();
		$form=new Form();
		$form->get($form_id);

		foreach( $form->form_elements as $element ) {
			$eid=$element->id;
			$this->log("vbf: Validating element $eid");
			$this->validatebyelement( $eid );
		}
		if (sizeof($this->errors) > 0) {
			$this->log("==== vbf failed ====");
			return false;
		}
		$this->log("vbf ok");
		return true; // There was one or more errors
	}

	/**
	 * I generate the <OPTIONS>...</OPTIONS>
	 * HTML for these validations
	 */
	function selectOptions($rules='ALL') {
		$html="";
		foreach($this->validations as $name => $valarray) {
			$html.="<option value=\"$name\">".$valarray[1]."</option>\n";
		}
		return $html;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $msg
	 */
	function log($msg) {
		$this->log->log("bfValidate: $msg");
	}

} // End of class bfValidate
?>