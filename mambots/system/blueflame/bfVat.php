<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfVat.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfVat {
	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
        function bfVat() {
                $this->__construct();
        }

	/**
	 * PHP5 constructor
	 */
        function __construct() {
        }

/*==============================================================================

PHP Port of jsvat.js 
Application:   Utility Function
Author:        John Gardner

Version:       V1.0
Date:          30th July 2005
Description:   Used to check the validity of an EU VAT number

Version:       V1.1
Date:          3rd August 2005
Description:   Lithunian legal entities & Maltese check digit checks added.

Version:       V1.2
Date:          20th October 2005
Description:   Italian checks refined (thanks Matteo Mike Peluso).

Version:       V1.3
Date:          16th November 2005
Description:   Error in GB numbers ending in 00 fixed (thanks Guy Dawson).

Version:       V1.4
Date:          28th September 2005
Description:   EU-type numbers added.
  
Parameters:    toCheck - VAT number be checked. 

This function checks the value of the parameter for a valid European VAT number. 

If the number is found to be invalid format, the function returns a value of 
false. Otherwise it reurns the VAT number re-formatted.
  
Example call:
  
  if (checkVATNumber (myVATNumber)) 
      alert ("VAT number has a valid format")
  else 
      alert ("VAT number has invalid format");
                    
------------------------------------------------------------------------------*/

  // Array holds the regular expressions for the valid VAT number
  var $vatexp = Array();

  var $defCCode = "GB";

function checkVATNumber ($toCheck) {
 
  
  // To change the default country (e.g. from the UK to Germany - DE):
  //    1.  Change the country code in the defCCode variable below to "DE".
  //    2.  Remove the question mark from the regular expressions associated 
  //        with the UK VAT number: i.e. "(GB)?" -> "(GB)"
  //    3.  Add a question mark into the regular expression associated with
  //        Germany's number following the country code: i.e. "(DE)" -> "(DE)?"
  
// echo "Checking $toCheck\n";

  $this->vatexp[]= "/^(AT)U(\d{8})$/";                           //** Austria
  $this->vatexp[]= "/^(BE)(\d{9}\d?)$/";                         //** Belgium 
  $this->vatexp[]= "/^(CY)\d{8}[A-Z]$/";                         // Cyprus 
  $this->vatexp[]= "/^(CZ)(\d{8,10})(\d{3})?$/";                 //** Czech Republic
  $this->vatexp[]= "/^(DK)((\d{8}))$/";                          //** Denmark 
  $this->vatexp[]= "/^(EE)(\d{9})$/";                            //** Estonia 
  $this->vatexp[]= "/^(FI)(\d{8})$/";                            //** Finland 
  $this->vatexp[]= "/^(FR)(\d{11})$/";                           //** France (1)
  $this->vatexp[]= "/^(FR)([(A-H)|(J-N)|(P-Z)]\d{10})$/";          // France (2)
  $this->vatexp[]= "/^(FR)(\d[(A-H)|(J-N)|(P-Z)]\d{9})$/";         // France (3)
  $this->vatexp[]= "/^(FR)([(A-H)|(J-N)|(P-Z)]{2}\d{9})$/";        // France (4)
  $this->vatexp[]= "/^(DE)(\d{9})$/";                            //** Germany 
  $this->vatexp[]= "/^(EL)(\d{8,9})$/";                          //** Greece 
  $this->vatexp[]= "/^(HU)(\d{8})$/";                            //** Hungary 
  $this->vatexp[]= "/^(IE)(\d{7}[A-W])$/";                       //** Ireland (1)
  $this->vatexp[]= "/^(IE)([7-9][A-Z]\d{5}[A-W])$/";             //** Ireland (2)
  $this->vatexp[]= "/^(IT)(\d{11})$/";                           //** Italy 
  $this->vatexp[]= "/^(LV)(\d{11})$/";                           //** Latvia 
  $this->vatexp[]= "/^(LT)(\d{9}|\d{12})$/";                     //** Lithunia
  $this->vatexp[]= "/^(LU)(\d{8})$/";                            //** Luxembourg 
  $this->vatexp[]= "/^(MT)(\d{8})$/";                            //** Malta
  $this->vatexp[]= "/^(NL)(\d{9})B\d{2}$/";                      //** Netherlands
  $this->vatexp[]= "/^(PL)(\d{10})$/";                           //** Poland
  $this->vatexp[]= "/^(PT)(\d{9})$/";                            //** Portugal
  $this->vatexp[]= "/^(RO)(\d{10})$/";                           // Romania
  $this->vatexp[]= "/^(SL)(\d{8})$/";                            //** Slovenia
  $this->vatexp[]= "/^(SK)(\d{9}|\d{10})$/";                     // Slovakia Republic
  $this->vatexp[]= "/^(ES)([A-Z]\d{8})$/";                       //** Spain (1)
  $this->vatexp[]= "/^(ES)(\d{8}[A-Z])$/";                       // Spain (2)
  $this->vatexp[]= "/^(ES)([A-Z]\d{7}[A-Z])$/";                  //** Spain (3)
  $this->vatexp[]= "/^(SE)(\d{10}\d[1-4])$/";                    //** Sweden
  $this->vatexp[]= "/^(GB)?(\d{9})$/";                           //** UK (1)
  $this->vatexp[]= "/^(GB)?(\d{9})\d{3}$/";                      //** UK (2)
  $this->vatexp[]= "/^(GB)?GD\d{3}$/";                           //** UK (3)
  $this->vatexp[]= "/^(GB)?HA\d{3}$/";                           //** UK (4)
  $this->vatexp[]= "/^(EU)(\d{9})$/";                            //** EU-type 

  // Load up the string to check
  $VATNumber = strtoupper($toCheck);
  
  // Remove spaces from the VAT number to help validation

  $VATNumber=preg_replace("/ /",'',$VATNumber);
  $VATNumber=preg_replace("/-/",'',$VATNumber);
  $VATNumber=preg_replace("/,/",'',$VATNumber);
  $VATNumber=preg_replace("/\./",'',$VATNumber);

// echo "Checking $VATNumber\n";
  // Assume we're not going to find a valid VAT number
  $valid = false;
  
  // Check the string against the types of VAT numbers
  $RegExp=array();

  for ($i=0; $i<sizeof($this->vatexp); $i++) {
	if (preg_match($this->vatexp[$i],$VATNumber,$RegExp)) {
      
      		$cCode = $RegExp[1];	// Isolate country code
      		$cNumber = $RegExp[2];  // Isolate the number
      		if (strlen($cCode) == 0) $cCode = $this->defCCode;    // Set up default country code

// echo "Matched ".$this->vatexp[$i]."\n";
// echo "cCode is $cCode\n";
// echo "cNumber is $cNumber\n";
                                 
      		// Now look at the check digits for those countries we know about.
		$method=$cCode."VATCheckDigit";
		if (method_exists($this,$method)) {
// echo "cCode cNumber is $cCode-$cNumber\n";
			$valid=$this->$method($cNumber);
		} else {
			$valid=true;
		}
		break;
      	} else {
		// echo "Failed to match ".$this->vatexp[$i]."against $VATNumber \n";
	}
    }
  return($valid);
}

function ATVATCheckDigit ($vatnumber) {

  // Checks the check digits of an Austrian VAT number.
  
  $total = 0;
  $multipliers = array(1,2,1,2,1,2,1);
  $temp = 0;
  
  // Extract the next digit and multiply by the appropriate multiplier.  
  for ($i = 0; $i < 7; $i++) {
    $temp = (int)($vatnumber[$i]) * $multipliers[$i];
    if ($temp > 9)
      $total = $total + floor($temp/10) + $temp%10;
    else
      $total = $total + $temp;
  }  
  
  // Establish check digit.
  $total = 10 - ($total+4) % 10; 
  if ($total == 10) $total = 0;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

function BEVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Belgium VAT number.
  
  // Nine digit numbers have a 0 inserted at the front.
  if (strlen($vatnumber) == 9) $vatnumber = "0".$vatnumber;
 $sum=97 - (int)substr($vatnumber,0,8) % 97 ;
 // echo "BE:$vatnumber - ". $sum ." compare with ".substr($vatnumber,8,3)."\n"; 
  if (97 - (int)substr($vatnumber,0,8) % 97 == (int)substr($vatnumber,8,3)) 
    return true;
  else 
    return false;
}

function CZVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Czech Republic VAT number.
  
  $total = 0;
  $multipliers = array(8,7,6,5,4,3,2);
  
  // Only do check digit validation for standard VAT numbers
  if (strlen($vatnumber) != 8) return true;
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int) $vatnumber[i] * $multipliers[i];
  
  // Establish check digit.
  $total = 11 - $total % 11;
  if ($total == 10) $total = 0; 
  if ($total == 11) $total = 1; 
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

function DEVATCheckDigit ($vatnumber) {

  // Checks the check digits of a German VAT number.
  
  $product = 10;
  $sum = 0;     
  $checkdigit = 0;                      
  for ($i = 0; $i < 8; $i++) {
    
    // Extract the next digit and implement perculiar algorithm!.
    // Not sure of translation with bracketing below
    $sum = ((int) $vatnumber[$i] + $product) % 10;
    if ($sum == 0) $sum = 10;
    $product = (2 * $sum) % 11;
  }
  
  // Establish check digit.  
  if (11 - $product == 10) 
  	$checkdigit = 0;
  else 
  	$checkdigit = 11 - $product;
  
  // Compare it with the last two characters of the VAT number. If the same, 
  // then it is a valid check digit.
  if ($checkdigit == (int) substr($vatnumber,8,2))
    return true;
  else 
    return false;
}

function DKVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Danish VAT number.
  
  $total = 0;
  $multipliers = array(2,7,6,5,4,3,2,1);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digit.
  $total = $total % 11;
  
  // The remainder should be 0 for it to be valid..
  if ($total == 0) 
    return true;
  else 
    return false;
}

function EEVATCheckDigit ($vatnumber) {

  // Checks the check digits of an Estonian VAT number.
  
  $total = 0;
  $multipliers = array(3,7,1,3,7,1,3,7);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits using modulus 10.
  $total = 10 - $total % 10;
  if ($total == 10) $total = 0;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,8,2))
    return true;
  else 
    return false;
}

function ELVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Greek VAT number.
  
  $total = 0;
  $multipliers = array(256,128,64,32,16,8,4,2);
  
  //eight character numbers should be prefixed with an 0.
  if (strlen($vatnumber) == 8) $vatnumber = "0" + $vatnumber;
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digit.
  $total = $total % 11;
  if ($total > 9) {$total = 0;};  
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,8,2)) 
    return true;
  else 
    return false;
}

function ESVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Spanish VAT number.
  
  $total = 0; 
  $temp = 0;
  $multipliers = array(2,1,2,1,2,1,2);
  $esexp = array ();
  $i = 0;

  $esexp[]="/^[A-H]\d{8}$/";
  $esexp[]="/^[N|P|Q|S]\d{7}[A-Z]$/";
  
  // With profit companies
  if (preg_match($esexp[0],$vatnumber)) {
  
    // Extract the next digit and multiply by the counter.
    for ($i = 0; $i < 7; $i++) {
      $temp = (int)$vatnumber[$i+1] * $multipliers[$i];
      if ($temp > 9) 
        $total = $total + floor($temp/10) + $temp%10;
      else 
        $total = $total + $temp;
    }   
    
    // Now calculate the check digit itself. 
    $total = 10 - $total % 10;
    if ($total == 10) {$total = 0;}
    
    // Compare it with the last character of the VAT number. If it is the same, 
    // then it's a valid check digit.
    if ($total == substr($vatnumber,8,2)) 
      return true;
    else 
      return false;
  }
  
  // Non-profit companies
  else if (preg_match($esexp[1],$vatnumber)) {
  
    // Extract the next digit and multiply by the counter.
    for ($i = 0; $i < 7; $i++) {
      $temp = (int)($vatnumber[$i+1]) * $multipliers[$i];
      if ($temp > 9) 
        $total = $total + floor($temp/10) + $temp%10;
      else 
        $total = $total + $temp;
    }    
    
    // Now calculate the check digit itself.
    $total = 10 - $total % 10;
    $total = chr($total+64);
    
    // Compare it with the last character of the VAT number. If it is the same, 
    // then it's a valid check digit.
    if ($total == (int)substr($vatnumber,8,2)) 
      return true;
    else 
      return false;
  }
  else return true;
}

function EUVATCheckDigit ($vatnumber) {

  // We know litle about EU numbers apart from the fact that the first 3 digits 
  // represent the country, and that there are nine digits in total.
  return true;
}

function FIVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Finnish VAT number.
  
  $total = 0; 
  $multipliers = array(7,9,10,5,8,4,2);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int)($vatnumber[$i]) * $multipliers[$i];
  
  // Establish check digit.
  $total = 11 - $total % 11;
  if ($total > 9) $total = 0;  
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  // echo "====== FI $total ".(int)substr($vatnumber,7,2)."\n";
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

// Have rewritten FRVAT as it didn;t seem to accept valid
// French VAT numbers. No validation just 1234567890 with 
// 1 and 2 being a character but not I or O
function FRVATCheckDigit ($vatnumber) {
  // echo "Checking $vatnumber\n";
  // Checks the check digits of a French VAT number.
  
  // 11 digits is fine
  if (preg_match("/^\d{11}$/",$vatnumber) ) {
	//echo "11 digits fine\n";
	return true;
	}
  // Last 9 digits needs further checks
  if (!preg_match("/^..\d{9}$/",$vatnumber) ) {
	//echo "last 9 not digits failure\n";
	return false;
	}
  // First and second characters must not be I or O (capital i or o)
  if ( ($vatnumber[0] == 'I') || ($vatnumber[1] == 'I') || ($vatnumber[0] == 'O') || ($vatnumber[1] == 'I') ) {
	//echo "IO in 1 and 2 failure\n";
	return false;
	}
  //Otherwise letter or digit, letter or digit, 9 digits is fine
  if (preg_match("/^[A-Z0-9][A-Z0-9]\d{9}$/",$vatnumber) ) {
	//echo "Matches XY123456789 ok\n";
	return true;
	}
  //echo "Bailing out!\n";
  return false;
}

function HUVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Hungarian VAT number.
  
  $total = 0;
  $multipliers = array(9,7,3,1,9,7,3);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digit.
  $total = 10 - $total % 10; 
  if ($total == 10) $total = 0;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

function IEVATCheckDigit ($vatnumber) {

  // Checks the check digits of an Irish VAT number.
  
  $total = 0; 
  $multipliers = array(8,7,6,5,4,3,2);
  
  // If the code is in the old format, we need to convert it to the new.
  if (preg_match("/^\d[A-Z]/",$vatnumber)) {
    $vatnumber = "0" + substr($vatnumber,2,5) + substr($vatnumber,0,2) + substr($vatnumber,7,2);
  }
    
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int)($vatnumber[$i]) * $multipliers[$i];
  
  // Establish check digit using modulus 23, and translate to char. equivalent.
  $total = $total % 23;
  if ($total == 0)
    $total = "W";
  else
    $total = chr($total+64);
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

function ITVATCheckDigit ($vatnumber) {

  // Checks the check digits of an Italian VAT number.
  
  $total = 0;
  $multipliers = array(1,2,1,2,1,2,1,2,1,2);
    
  // The last three digits are the issuing office, and cannot exceed more 201
  $temp=(int)substr($vatnumber,0,7);
  if ($temp==0) return false;
  $temp=(int)substr($vatnumber,7,4);
  if (($temp<1) || ($temp>201)) return false;
  
  // Extract the next digit and multiply by the appropriate  
  for ($i = 0; $i < 10; $i++) {
    $temp = (int)$vatnumber[$i] * $multipliers[$i];
    if ($temp > 9) 
      $total = $total + floor($temp/10) + $temp%10;
    else 
      $total = $total + $temp;
  }
  
  // Establish check digit.
  $total = 10 - $total % 10;
  if ($total > 9) {$total = 0;};  
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,10,2)) 
    return true;
  else 
    return false;
}

function LTVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Lithuanian VAT number.
  
  // Only do check digit validation for standard VAT numbers
  if (strlen($vatnumber) != 9) return true;
  
  // Extract the next digit and multiply by the counter+1.
  $total = 0;
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * ($i+1);
  
  // Can have a double check digit calculation!
  if ($total % 11 == 10) {
    $multipliers = array(3,4,5,6,7,8,9,1);
    $total = 0;
    for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  }
  
  // Establish check digit.
  $total = $total % 11;
  if ($total == 10) {$total = 0;}; 
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,8,2)) 
    return true;
  else 
    return false;
}

function LUVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Luxembourg VAT number.
  
  if ((int)substr($vatnumber,0,6) % 89 == (int)substr($vatnumber,6,2)) 
    return true;
  else 
    return false;
}

function LVVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Latvian VAT number.
  
  // Only check the legal bodies
  if (preg_match("/^[0-3]/",$vatnumber)) return true; 
  
  $total = 0;
  $multipliers = array(9,1,4,8,3,10,2,5,7,6);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 10; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits by getting modulus 11.
  if ($total%11 == 4 && $vatnumber[0] ==9) $total = $total - 45;
  if ($total%11 == 4) 
    $total = 4 - total%11;
  else if ($total%11 > 4) 
    $total = 14 - $total%11;
  else if ($total%11 < 4) 
    $total = 3 - $total%11;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,10,2)) 
    return true;
  else 
    return false;
}

function MTVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Maltese VAT number.
  
  $total = 0;
  $multipliers = array(3,4,6,7,8,9);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 6; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits by getting modulus 37.
  $total = 37 - $total % 37;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,6,3)) 
    return true;
  else 
    return false;
}

function NLVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Dutch VAT number.
  
  $total = 0;                                 // 
  $multipliers = array(9,8,7,6,5,4,3,2);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits by getting modulus 11.
  $total = $total % 11;
  if ($total > 9) {$total = 0;};  
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,8,2)) 
    return true;
  else 
    return false;
}

function PLVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Polish VAT number.
  
  $total = 0;
  $multipliers = array(6,5,7,2,3,4,5,6,7);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 9; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits subtracting modulus 11 from 11.
  $total = $total % 11;
  if ($total > 9) {$total = 0;};
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,9,2)) 
    return true;
  else 
    return false;
}

function PTVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Portugese VAT number.
  
  $total = 0;
  $multipliers = array(9,8,7,6,5,4,3,2);
  
  // Extract the next digit and multiply by the counter)
  for ($i = 0; $i < 8; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits subtracting modulus 11 from 11.
  $total = 11 - $total % 11;
  if ($total > 9) $total = 0;
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ( $total == (int)substr($vatnumber,8,2) ) 
    return true;
  else 
    return false;
}

function SEVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Swedish VAT number.
  
  $total = 0;
  $multipliers = array(2,1,2,1,2,1,2,1,2);
  $temp = 0;
  
  // Extract the next digit and multiply by the appropriate multiplier.
  for ($i = 0; $i < 9; $i++) {
    $temp = (int)($vatnumber[$i]) * $multipliers[$i];
    if ($temp > 9)
      $total = $total + floor($temp/10) + $temp%10;
    else 
      $total = $total + $temp;
  }
  
  // Establish check digits by subtracting mod 10 of total from 10.
  $total = 10 - ($total % 10); 
  if ($total == 10) $total = 0;
  
  // Compare it with the 10th character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  // echo "SE Comparing $total with ".substr($vatnumber,9,1)."\n";
  if ($total == (int)substr($vatnumber,9,1)) 
    return true;
  else 
    return false;
}

function SKVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Slovak VAT number.
  
  $total = 0; 
  $multipliers = array(8,7,6,5,4,3,2);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 3; $i < 9; $i++) {
    $total = $total + (int)($vatnumber[$i]) * $multipliers[$i-3];
  }  
  
  // Establish check digits by getting modulus 11.
  $total = 11 - $total % 11;
  if ($total > 9) $total = $total - 10;  
  
  // Compare it with the last character of the VAT number. If it is the same, 
  // then it's a valid check digit.
  if ($total == (int)substr($vatnumber,9,2)) 
    return true;
  else 
    return false;
}

function SLVATCheckDigit ($vatnumber) {

  // Checks the check digits of a Slovenian VAT number.
  
  $total = 0; 
  $multipliers = array(8,7,6,5,4,3,2);
  
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits by subtracting 97 from total until negative.
  $total = 11 - $total % 11;
  if ($total > 9) {$total = 0;};  
  
  // Compare the number with the last character of the VAT number. If it is the 
  // same, then it's a valid check digit.
  if ($total == (int)substr($vatnumber,7,2)) 
    return true;
  else 
    return false;
}

function UKVATCheckDigit ($vatnumber) {

  // Checks the check digits of a UK VAT number.
  
  // Only inspect check digit of 9 character numbers
  if (strlen($vatnumber) != 9) return true;
  
  $multipliers = array(8,7,6,5,4,3,2);
  $total = 0;
    
  // Extract the next digit and multiply by the counter.
  for ($i = 0; $i < 7; $i++) $total = $total + (int)$vatnumber[$i] * $multipliers[$i];
  
  // Establish check digits by subtracting 97 from total until negative.
  while ($total > 0) {$total = $total - 97;}    
  
  // Get the absolute value and compare it with the last two characters of the
  // VAT number. If the same, then it is a valid check digit.
  $total = abs($total);
  if ($total == (int)substr($vatnumber,7,3)) 
    return true;
  else  
    return false;
  }
} // End of class bfVat
?>
