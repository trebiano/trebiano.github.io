<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfVerify.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
bfLoad('bfCreditCard');
bfLoad('bfVat');

class bfVerify {
	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfVerify() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $number
	 * @param unknown_type $comparison
	 * @return unknown
	*/
	function numbergreaterthan( $number, $comparison ) {
		if ($number > $comparison) return true;
		return false;
	}

	function numberlessthan( $number, $comparison ) {
		if ($number < $comparison) return true;
		return false;
	}

	/**
     * Enter description here...
     *
     * @param unknown_type $expression
     * @param unknown_type $stringtomatch
     * @return unknown
     */
	function regex( $expression, $stringtomatch ) {
		if (preg_match('/^'.$expression.'$/',$stringtomatch))
		return true;
		return false;
	}

	/**
	 * Comparing reals is difficult, so we'll use a tolerance
	 * to compare the square of the difference between the
	 * 2 numbers
	 *
	 * @param unknown_type $number
	 * @param unknown_type $comparison
	 * @return unknown
	 */
	function numberequals( $number, $comparison ) {
		$tolerance=0.00000000000000001;
		$delta=$number - $comparison;
		$delta *= $delta;
		if ($delta < $tolerance) return true;
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @GREG This will never work - when we pass into this function it will always be a string!!
	 *
	 *
	 * @param unknown_type $number
	 * @return unknown
	 */
	function isinteger( $number ) {
		if( preg_match('/^[-+]*[0-9]+$/',$number) ) return true;
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $number
	 * @return unknown
	 */
	function isfloat( $number ) {
		return( is_float($number) );
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $number
	 * @return unknown
	 */
	function isnumeric( $number ) {
		return( is_numeric($number) );
	}

	/**
	 * Enter description here...
	 *
	 * @author Phil - Recently renamed to ISemailaddressby
	 * @param unknown_type $address
	 * @return unknown
	 */
	function isemailaddress( $address ) {
		$validatePattern = "/^[a-z0-9\-_~\.]+@(([a-z0-9\-]+\.)+[a-z]+)$/ix";

		if (preg_match($validatePattern, $address, $matches)) {
			// check the domain part is a valid host name
			if (gethostbynamel($matches[1] . '.') === false) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @author Phil - Recently renamed to ISemailaddressbydns
	 * @param unknown_type $address
	 * @return unknown
	 */
	function isemailaddressbydns( $address ) {
		// take a given email address and split it into the username and domain.
		list($userName, $mailDomain) = split("@", $address);
		if (bfString::strlen($userName) == 0) return false;
		if (checkdnsrr($mailDomain, "MX")) return true;
		return false;
	}

	/**
	 * Blank - Required Field
	 *
	 * @param unknown_type $input
	 * @return unknown
	 */
	function isblank( $input ) {
		if ($input == null || $input == '') return(true);
		return false;
	}

	/**
	 * Blank - Required Field
	 *
	 * @param unknown_type $input
	 * @return unknown
	 */
	function isnotblank( $input ) {
		if ($input == null || $input == '') return(false);
		return true;
	}

	/**
	 * Checkbox field is checked
	 *
	 * @param unknown_type $input
	 * @return unknown
	 */
	function ischecked( $input ) {
		if ($input == "on") return true;
		return false;
	}

	/**
	 * Checkbox field is not checked
	 *
	 * @param unknown_type $input
	 * @return unknown
	 */
	function isnotchecked( $input = '' ) {
		if ($input == "on") return false;
		return true;
	}

	/**
	 * Length - Input to meet a certian length
	 *
	 * @param unknown_type $string
	 * @param unknown_type $length
	 * @return unknown
	 */
	function stringlength( $string, $length ) {
		if (bfString::strlen($string) == $length) return(true);
		return false;
	}

	/**
	 * Length - Input to exceed a certian length
	 *
	 * @param unknown_type $string
	 * @param unknown_type $length
	 * @return unknown
	 */
	function stringlengthgreaterthan( $string, $length ) {
		if (bfString::strlen($string) > $length) return(true);
		return false;
	}

	/**
	 * Length - Input to be less than a certian length
	 *
	 * @param unknown_type $s1
	 * @param unknown_type $s2
	 * @return unknown
	 */
	function stringlengthlessthan( $string, $length ) {
		if (bfString::strlen($string) < $length) return(true);
		return false;
	}

	/**
	 * Equalto - Input must be equal to a input of another field (password validation), also < > >= <= != etc.
	 *
	 * @param unknown_type $input
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @return unknown
	 */

	function equalto( $s1, $s2 ) {
		return( $s1 == $s2 );
	}

	/**
	 * Range - check $input >= $from and $input <= $to
	 *
	 * @param unknown_type $input
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @return unknown
	 */
	function range( $input, $from=0, $to=0 ) {
		if (!isset($to) || $to == '' || $to == null) $to ="0";
		if ( $input >= $from ) {
			if ( $input <= $to ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * I.P - Checks if a valid IP address has been submitted e.g. xxx.xxx.xxx.xxx
	 *
	 * @param unknown_type $addr
	 * @return unknown
	 */
	function ipaddress( $addr ) {
		// Need something good here for verifying N < 255
		// Basic NN.NNN.NNNNNN.N type check
		if (!preg_match('/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/',$addr)) return false;
		$numbers=split('\.',$addr);
		if (sizeof($numbers) < 4) return false;

		if (bfString::strlen($numbers[0]) < 1) return false;
		if (bfString::strlen($numbers[1]) < 1) return false;
		if (bfString::strlen($numbers[2]) < 1) return false;
		if (bfString::strlen($numbers[3]) < 1) return false;

		if (bfString::strlen($numbers[0]) > 3) return false;
		if (bfString::strlen($numbers[1]) > 3) return false;
		if (bfString::strlen($numbers[2]) > 3) return false;
		if (bfString::strlen($numbers[3]) > 3) return false;

		if ((int) $numbers[0] > 255) return false;
		if ((int) $numbers[1] > 255) return false;
		if ((int) $numbers[2] > 255) return false;
		if ((int) $numbers[3] > 255) return false;

		if ((int) $numbers[0] < 0) return false;
		if ((int) $numbers[1] < 0) return false;
		if ((int) $numbers[2] < 0) return false;
		if ((int) $numbers[3] < 0) return false;

		return true;
	}

	function UKNINumber( $nino ) {
		// 1. Must be 9 characters.
		// 2. First 2 characters must be alpha.
		// 3. Next 6 characters must be numeric.
		// 4. Final character can be A, B, C, D or space.
		// 5. First character must not be D,F,I,Q,U or V
		// 6. Second characters must not be D, F, I, O, Q, U or V.
		// 7. First 2 characters must not be combinations of GB, NK, TN or ZZ (the term combinations covers both GB and BG etc.)
		$nino=bfString::strtoupper(trim($nino));
		if (bfString::strlen($nino) != 9) return false;
		if (!preg_match('/^[A-Z][A-Z]\d{6}/',$nino)) return false;
		if (!preg_match('/[ABCD ]$/',$nino)) return false;
		if (preg_match('/^[DFIQUV]$/',$nino)) return false;
		if (preg_match('/^.[DFIQUVO]/',$nino)) return false;
		if (preg_match('/^GB/',$nino)) return false;
		if (preg_match('/^BG/',$nino)) return false;
		if (preg_match('/^NK/',$nino)) return false;
		if (preg_match('/^KN/',$nino)) return false;
		if (preg_match('/^TN/',$nino)) return false;
		if (preg_match('/^NT/',$nino)) return false;
		if (preg_match('/^ZZ/',$nino)) return false;
		return true;
	}

	/**
     *  Check US Social Security Number (SSN)
     * Number - Checks if a input is a int or a float, also checks if number is within a rage e.g. age between 0-100
	 * Numeric - Validate numbers a strings e.g. to check the length of the number
	 * Alnum - Input must not have any non-alpha-numeric chars
	 * Decimal - Checks if the input was a decimal
	 * Decimalr - Validates complex decimals e.g. Radio Frequancy
	 * Money - Chacks for a valid currency e.g. £45,000.00
	 * Comparison - Checks for the same input in two elements (password/e-mail validation)
     *
     * @param string $ssn
     * @return bool
     */
	function SSN( $ssn ) {
		if (preg_match("/^\d{3}-\d{2}-\d{4}$/", $ssn)){
			return true ;
		} else {
			return false;
		}
	}


	/**
	 * Credit Card - A credit card type must be selected from a dropdown list
	 * and a valid credit card number must also be entered into a textbox
	 *
	 * @param unknown_type $number
	 * @return unknown
	 */
	function CreditCardNumber( $number ) {
		$cc=new credit_card();
		return $cc->validate( $number );
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $cctype
	 * @param unknown_type $number
	 * @return unknown
	 */
	function CreditCardType( $cctype,$number ) {
		$cc=new credit_card();
		$type=$cc->identify( $number );
		return( !strcmp( trim(bfString::strtoupper($type)), trim(bfString::strtoupper($cctype)) ) );
	}

	/**
     * US Zip code
     *
     * @param unknown_type $zip
     * @return unknown
     */
	function uszip( $zip ) {
		if (preg_match("/^\d{5}$/", $zip)) return( true );
		if (preg_match("/^\d{5}-\d{4}$/", $zip)) return( true );
		return( false );
	}

	/**
         * Alnum - Input must not have any non-alpha-numeric chars
         *
         * @param unknown_type $word
         * @return unknown
         */
	function alphanumeric( $word ) {
		if (preg_match("/^\w*$/",$word)) return true;
		return false;
	}
	//Decimal - Checks if the input was a decimal

	/**
         * Enter description here...
         *
         * @param unknown_type $number
         * @return unknown
         */
	function decimal($number) {
		if (preg_match('/^-?\d*\.+\d*$/',$number))
		return true;
		return false;
	}

	// Check that a date format is dd/mm/yyyy or mm/dd/yyyy or yyyy/mm/dd
	/**
         * Enter description here...
         *
         * @param unknown_type $date
         * @return unknown
         */
	function dateformat( $date ) {
		$datearray=array();
		if (preg_match('/^(\d{2}).(\d{2}).(\d{4})$/',$date,$datearray)) {
			$dd=$datearray[1];
			$mm=$datearray[2];
			$yyyy=$datearray[3];
			if ($this->checkdate($mm,$dd,$yyyy)) return true;
		}
		if (preg_match('/^(\d{2}).(\d{2}).(\d{4})$/',$date,$datearray)) {
			$mm=$datearray[1];
			$dd=$datearray[2];
			$yyyy=$datearray[3];
			if ($this->checkdate($mm,$dd,$yyyy)) return true;
		}
		if (preg_match('/^(\d{4}).(\d{2}).(\d{2})$/',$date,$datearray)) {
			$yyyy=$datearray[1];
			$mm=$datearray[2];
			$dd=$datearray[3];
			if ($this->checkdate($mm,$dd,$yyyy)) return true;
		}
		return false;
	}

	//Eitheror - Forces user to enter data to one of two elements
	/**
         * Enter description here...
         *
         * @param unknown_type $first
         * @param unknown_type $second
         * @return unknown
         */
	function eitheror( $first, $second ) {
		if (($first != '') && ($second == '')) return true;
		if (($first == '') && ($second != '')) return true;
		return false;
	}

	//Phone UD - Chacks for a valid US phone number
	//Phone UK - Checks for a valid UK phone number
	//Date - Chacks for a valid date (dd/mm/yyyy, mm/dd/yyyy, yyyy/mm/dd)
	//Select - Forces a selection from a list/dropdown box
	//Selectm - Forces a multiple selection from a listbox
	//Selecti - Forces a selection of certian elements in a dropdown/list box
	//Checkbox - Checks to so if checkboxes have been checked
	//Radio Button - Requires a input from the user
	//Atleast - Forces user to enter data into x number of elements
	//AllOrNone - Forces users to enter data into all fields or none at all
	//File - Forces a user to submit a certian type of file in a file upload box e.g. PDF's only
	//Custom - Allows administrator to create their own custom validation rules
	//CaZip - Checks for a valid Canadian zip code

	//UkPost - checks for a valid UK postcode
	/**
         * The format of UK postcodes is generally:
         *
         *    A9 9AA
         *    A99 9AA
         *    A9A 9AA
         *    AA9 9AA
         *    AA99 9AA
         *    AA9A 9AA
         */

	function ukpostcode( $code ) {
		$postcode = bfString::strtoupper(str_replace(chr(32),'',$code));
		if(ereg("^(GIR0AA)|(TDCU1ZZ)|((([A-PR-UWYZ][0-9][0-9]?)|"
		."(([A-PR-UWYZ][A-HK-Y][0-9][0-9]?)|"
		."(([A-PR-UWYZ][0-9][A-HJKSTUW])|"
		."([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))))"
		."[0-9][ABD-HJLNP-UW-Z]{2})$", $postcode)) return true;
		return false;
	}

	//GermanPost - Checks for a valid German postcode
	//SwissPost - Checks for a valid Swiss postcode
	//URL - Checks for a valid URL (http,ftp etc.)
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $URL
	 * @return unknown
	 */
	function URL( $URL ) {
		return($this->validateUrlSyntax($URL));
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $num
	 * @return unknown
	 */
	function vatnumber( $num ) {
		$v=new bfVat();
		return $v->checkVATNumber( $num );
	}

	/*
	Functions Included In This File:
	validateUrlSyntax()
	validateEmailSyntax()
	validateFtpSyntax()
	*/

	/*
	About validateUrlSyntax():
	This function will verify if a http URL is formatted properly, returning
	either with true or false.

	I used rfc #2396 URI: Generic Syntax as my guide when creating the
	regular expression. For all the details see the comments below.


	Usage:
	validateUrlSyntax( url_to_check[, options])

	url_to_check - string - The url to check

	options - string - A optional string of options to set which parts of
	the url are required, optional, or not allowed. Each option
	must be followed by a "+" for required, "?" for optional, or
	"-" for not allowed.

	s - Scheme. Allows "+?-", defaults to "s?"
	H - http:// Allows "+?-", defaults to "H?"
	S - https:// (SSL). Allows "+?-", defaults to "S?"
	E - mailto: (email). Allows "+?-", defaults to "E-"
	F - ftp:// Allows "+?-", defaults to "F-"
	Dependant on scheme being enabled
	u - User section. Allows "+?-", defaults to "u?"
	P - Password in user section. Allows "+?-", defaults to "P?"
	Dependant on user section being enabled
	a - Address (ip or domain). Allows "+?-", defaults to "a+"
	I - Ip address. Allows "+?-", defaults to "I?"
	If I+, then domains are disabled
	If I-, then domains are required
	Dependant on address being enabled
	p - Port number. Allows "+?-", defaults to "p?"
	f - File path. Allows "+?-", defaults to "f?"
	q - Query section. Allows "+?-", defaults to "q?"
	r - Fragment (anchor). Allows "+?-", defaults to "r?"

	Paste the funtion code, or include_once() this template at the top of the page
	you wish to use this function.


	Examples:
	validateUrlSyntax('http://george@www.cnn.com/#top')

	validateUrlSyntax('https://games.yahoo.com:8080/board/chess.htm?move=true')

	validateUrlSyntax('http://www.hotmail.com/', 's+u-I-p-q-r-')

	validateUrlSyntax('/directory/file.php#top', 's-u-a-p-f+')


	if (validateUrlSyntax('http://www.canowhoopass.com/', 'u-'))
	{
	echo 'URL SYNTAX IS VERIFIED';
	} else {
	echo 'URL SYNTAX IS ILLEGAL';
	}


	Last Edited:
	December 15th 2004


	Changelog:
	December 15th 2004
	-Added new TLD's - .jobs, .mobi, .post and .travel. They are official, but not yet active.

	August 31th 2004
	-Fixed bug allowing empty username even when it was required
	-Changed and added a few options to add extra schemes
	-Added mailto: ftp:// and http:// options
	-https option was 'l' now it is 'S' (capital)
	-Added password option. Now passwords can be disabled while usernames are ok (for email)
	-IP Address option was 'i' now it is 'I' (capital)
	-Options are now case sensitive
	-Added validateEmailSyntax() and validateFtpSyntax() functions below<br>

	August 27th, 2004
	-IP group range is more specific. Used to allow 0-299. Now it is 0-255
	-Port range more specific. Used to allow 0-69999. Now it is 0-65535<br>
	-Fixed bug disallowing 'i-' option.<br>
	-Changed license to GPL

	July 8th, 2004
	-Fixed bug disallowing 'l-' option. Thanks Dr. Cheap

	June 15, 2004
	-Added options parameter to make it easier for people to plug the function in
	without needed to rework the code.
	-Split the example application away from the function

	June 1, 2004
	-Complete rewrite
	-Now more modular
	-Easier to disable sections
	-Easier to port to other languages
	-Easier to port to verify email addresses
	-Uses only simple regular expressions so it is more portable
	-Follows RFC closer for domain names. Some "play" domains may break
	-Renamed from 'verifyUrl()' to 'validateUrlSyntax()'
	-Removed extra code which added 'http://' and trailing '/' if it was missing
	-That code was better suited for a massaging function, not verifying
	-Bug fixes:
	-Now splits up and forces '/path?query#fragment' order
	-No longer requires a path when using a query or fragment

	August 29, 2003
	-Allowed port numbers above 9999. Now allows up to 69999

	Sometime, 2002
	-Added new top level domains
	-aero, coop, museum, name, info, biz, pro

	October 5, 2000
	-First Version


	Intentional Limitations:
	-Does not verify url actually exists. Only validates the syntax
	-Strictly follows the RFC standards. Some urls exist in the wild which will
	not validate. Including ones with square brackets in the query section '[]'


	Known Problems:
	-None at this time


	Author(s):
	Rod Apeldoorn - rod(at)canowhoopass(dot)com


	Homepage:
	http://www.canowhoopass.com/


	Thanks!:
	-WEAV -Several members of Weav helped to test - http://weav.bc.ca/
	-There were also a number of emails from other developers expressing
	thanks and suggestions. It is nice to be appreciated. Thanks!


	License:
	Copyright 2004, Rod Apeldoorn

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or (at
	your option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	General Public License for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

	To view the license online, go to: http://www.gnu.org/copyleft/gpl.html


	Alternate Commercial Licenses:
	For information in regards to alternate licensing, contact me.
	*/


	// BEGINNING OF validateUrlSyntax() function
	/**
 * Enter description here...
 *
 * @param unknown_type $urladdr
 * @param unknown_type $options
 * @return unknown
 */
	function validateUrlSyntax( $urladdr, $options="" ){

		// Force Options parameter to be lower case
		// DISABLED PERMAMENTLY - OK to remove from code
		//    $options = bfString::strtolower($options);

		// Check Options Parameter
		if (!ereg( '^([sHSEFuPaIpfqr][+?-])*$', $options ))
		{
			trigger_error("Options attribute malformed", E_USER_ERROR);
		}

		// Set Options Array, set defaults if options are not specified
		// Scheme
		if (bfString::strpos( $options, 's') === false) $aOptions['s'] = '?';
		else $aOptions['s'] = bfString::substr( $options, bfString::strpos( $options, 's') + 1, 1);
		// http://
		if (bfString::strpos( $options, 'H') === false) $aOptions['H'] = '?';
		else $aOptions['H'] = bfString::substr( $options, bfString::strpos( $options, 'H') + 1, 1);
		// https:// (SSL)
		if (bfString::strpos( $options, 'S') === false) $aOptions['S'] = '?';
		else $aOptions['S'] = bfString::substr( $options, bfString::strpos( $options, 'S') + 1, 1);
		// mailto: (email)
		if (bfString::strpos( $options, 'E') === false) $aOptions['E'] = '-';
		else $aOptions['E'] = bfString::substr( $options, bfString::strpos( $options, 'E') + 1, 1);
		// ftp://
		if (bfString::strpos( $options, 'F') === false) $aOptions['F'] = '-';
		else $aOptions['F'] = bfString::substr( $options, bfString::strpos( $options, 'F') + 1, 1);
		// User section
		if (bfString::strpos( $options, 'u') === false) $aOptions['u'] = '?';
		else $aOptions['u'] = bfString::substr( $options, bfString::strpos( $options, 'u') + 1, 1);
		// Password in user section
		if (bfString::strpos( $options, 'P') === false) $aOptions['P'] = '?';
		else $aOptions['P'] = bfString::substr( $options, bfString::strpos( $options, 'P') + 1, 1);
		// Address Section
		if (bfString::strpos( $options, 'a') === false) $aOptions['a'] = '+';
		else $aOptions['a'] = bfString::substr( $options, bfString::strpos( $options, 'a') + 1, 1);
		// IP Address in address section
		if (bfString::strpos( $options, 'I') === false) $aOptions['I'] = '?';
		else $aOptions['I'] = bfString::substr( $options, bfString::strpos( $options, 'I') + 1, 1);
		// Port number
		if (bfString::strpos( $options, 'p') === false) $aOptions['p'] = '?';
		else $aOptions['p'] = bfString::substr( $options, bfString::strpos( $options, 'p') + 1, 1);
		// File Path
		if (bfString::strpos( $options, 'f') === false) $aOptions['f'] = '?';
		else $aOptions['f'] = bfString::substr( $options, bfString::strpos( $options, 'f') + 1, 1);
		// Query Section
		if (bfString::strpos( $options, 'q') === false) $aOptions['q'] = '?';
		else $aOptions['q'] = bfString::substr( $options, bfString::strpos( $options, 'q') + 1, 1);
		// Fragment (Anchor)
		if (bfString::strpos( $options, 'r') === false) $aOptions['r'] = '?';
		else $aOptions['r'] = bfString::substr( $options, bfString::strpos( $options, 'r') + 1, 1);


		// Loop through options array, to search for and replace "-" to "{0}" and "+" to ""
		foreach($aOptions as $key => $value)
		{
			if ($value == '-')
			{
				$aOptions[$key] = '{0}';
			}
			if ($value == '+')
			{
				$aOptions[$key] = '';
			}
		}

		// DEBUGGING - Unescape following line to display to screen current option values
		// echo '<pre>'; print_r($aOptions); echo '</pre>';


		// Preset Allowed Characters
		$alphanum    = '[a-zA-Z0-9]';  // Alpha Numeric
		$unreserved  = '[a-zA-Z0-9_.!~*' . '\'' . '()-]';
		$escaped     = '(%[0-9a-fA-F]{2})'; // Escape sequence - In Hex - %6d would be a 'm'
		$reserved    = '[;/?:@&=+$,]'; // Special characters in the URI

		// Beginning Regular Expression
		// Scheme - Allows for 'http://', 'https://', 'mailto:', or 'ftp://'
		$scheme            = '(';
		if     ($aOptions['H'] === '') { $scheme .= 'http://'; }
		elseif ($aOptions['S'] === '') { $scheme .= 'https://'; }
		elseif ($aOptions['E'] === '') { $scheme .= 'mailto:'; }
		elseif ($aOptions['F'] === '') { $scheme .= 'ftp://'; }
		else
		{
			if ($aOptions['H'] === '?') { $scheme .= '|(http://)'; }
			if ($aOptions['S'] === '?') { $scheme .= '|(https://)'; }
			if ($aOptions['E'] === '?') { $scheme .= '|(mailto:)'; }
			if ($aOptions['F'] === '?') { $scheme .= '|(ftp://)'; }
			$scheme = str_replace('(|', '(', $scheme); // fix first pipe
		}
		$scheme            .= ')' . $aOptions['s'];
		// End setting scheme

		// User Info - Allows for 'username@' or 'username:password@'. Note: contrary to rfc, I removed ':' from username section, allowing it only in password.
		//   /---------------- Username -----------------------\  /-------------------------------- Password ------------------------------\
		$userinfo          = '((' . $unreserved . '|' . $escaped . '|[;&=+$,]' . ')+(:(' . $unreserved . '|' . $escaped . '|[;:&=+$,]' . ')+)' . $aOptions['P'] . '@)' . $aOptions['u'];

		// IP ADDRESS - Allows 0.0.0.0 to 255.255.255.255
		$ipaddress         = '((((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9]))\.){3}((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9])))';

		// Tertiary Domain(s) - Optional - Multi - Although some sites may use other characters, the RFC says tertiary domains have the same naming restrictions as second level domains
		$domain_tertiary   = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)*';

		// Second Level Domain - Required - First and last characters must be Alpha-numeric. Hyphens are allowed inside.
		$domain_secondary  = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)';

		/* // This regex is disabled on purpose in favour of the more exact version below
		// Top Level Domain - First character must be Alpha. Last character must be AlphaNumeric. Hyphens are allowed inside.
		$domain_toplevel   = '([a-zA-Z](([a-zA-Z0-9-]*)[a-zA-Z0-9])?)';
		*/

		// Top Level Domain - Required - Domain List Current As Of December 2004. Use above escaped line to be forgiving of possible future TLD's
		$domain_toplevel   = '(aero|biz|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|post|pro|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ax|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)';


		// Address can be IP address or Domain
		if ($aOptions['I'] === '{0}') {       // IP Address Not Allowed
			$address       = '(' . $domain_tertiary . $domain_secondary . $domain_toplevel . ')';
		} elseif ($aOptions['I'] === '') {  // IP Address Required
			$address       = '(' . $ipaddress . ')';
		} else {                            // IP Address Optional
			$address       = '((' . $ipaddress . ')|(' . $domain_tertiary . $domain_secondary . $domain_toplevel . '))';
		}
		$address = $address . $aOptions['a'];

		// Port Number - :80 or :8080 or :65534 Allows range of :0 to :65535
		//    (0-59999)         |(60000-64999)   |(65000-65499)    |(65500-65529)  |(65530-65535)
		$port_number       = '(:(([0-5]?[0-9]{1,4})|(6[0-4][0-9]{3})|(65[0-4][0-9]{2})|(655[0-2][0-9])|(6553[0-5])))' . $aOptions['p'];

		// Path - Can be as simple as '/' or have multiple folders and filenames
		$path              = '(/((;)?(' . $unreserved . '|' . $escaped . '|' . '[:@&=+$,]' . ')+(/)?)*)' . $aOptions['f'];

		// Query Section - Accepts ?var1=value1&var2=value2 or ?2393,1221 and much more
		$querystring       = '(\?(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['q'];

		// Fragment Section - Accepts anchors such as #top
		$fragment          = '(#(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['r'];


		// Building Regular Expression
		$regexp = '^' . $scheme . $userinfo . $address . $port_number . $path . $querystring . $fragment . '$';

		// DEBUGGING - Uncomment Line Below To Display The Regular Expression Built
		// echo '<pre>' . htmlentities(wordwrap($regexp,70,"\n",1)) . '</pre>';

		// Running the regular expression
		if (eregi( $regexp, $urladdr ))
		{
			return true; // The domain passed
		}
		else
		{
			return false; // The domain didn't pass the expression
		}

	} // END Function validateUrlSyntax()

} // End of class bfVerify
?>
