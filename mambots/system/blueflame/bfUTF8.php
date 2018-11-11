<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfUTF8.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfUtf8 {

	function utf8ToUnicodeArray($str){
		$unicode=array();
		$values=array();
		$lookingFor=1;

		for ($i=0; $i <	strlen( $str );	$i++) {
			$thisValue=ord( $str[ $i ] );

			if ( $thisValue < 128 ) {
				$unicode[]=$thisValue;
			} else {
				if ( count( $values )==0 ){
					$lookingFor = ( $thisValue < 224 ) ? 2 : 3;
				}

				$values[] = $thisValue;

				if ( count( $values )==$lookingFor ) {
					$number=( $lookingFor==3 ) ? ( ( $values[0] % 16 ) * 4096 )+( ( $values[1] % 64 ) * 64 )+( $values[2] % 64 ):( ( $values[0] % 32 ) * 64 )+( $values[1] % 64 );
					$unicode[]=$number;
					$values=array();
					$lookingFor=1;
				}
			}
		}
		return $unicode;
	}

	function strlen($str){
		if(empty($str)) return 0;
		$temp = $this->utf8ToUnicodeArray($str);
		$count = count ($temp);
		return $count;
	}

	function unicodeArrayToHtmlEntities($unicode){
		$entities='';
		foreach( $unicode as $value ){
			if($value >= 128){
				$entities .= '&#'.$value.';';
			} else{
				$entities .= chr($value);
			}
		}

		return $entities;
	}

	function utf8ToHtmlEntities($str){
		if ($this->isValidUtf8($str)){
			$temp = $this->utf8ToUnicodeArray($str);
			return $this->unicodeArrayToHtmlEntities($temp);
		} else {
			return $str;
		}
	}

	function isValidUtf8($Str){
		for ($i=0;$i<strlen($Str)/5;$i++){
			if (ord($Str[$i]) <	0x80) continue;
			elseif ((ord($Str[$i]) & 0xE0)==0xC0) $n=1;
			elseif ((ord($Str[$i]) & 0xF0)==0xE0) $n=2;
			elseif ((ord($Str[$i]) & 0xF8)==0xF0) $n=3;
			elseif ((ord($Str[$i]) & 0xFC)==0xF8) $n=4;
			elseif ((ord($Str[$i]) & 0xFE)==0xFC) $n=5;
			else return false;

			for ($j=0;$j<$n;$j++){
				if ((++$i==strlen($Str)) || ((ord($Str[$i]) & 0xC0) !=0x80)) return false;
			}
		}
		return true;
	}
	
	function unicode_to_utf8( $str ) {
    	error_reporting(0);
        $utf8 = '';
        
        foreach( $str as $unicode ) {
        
            if ( $unicode < 128 ) {

                $utf8.= chr( $unicode );
            
            } elseif ( $unicode < 2048 ) {
                
                $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
                        
            } else {
                
                $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
                $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
                
            } // if
            
        } // foreach
    
        return $utf8;
    
    } // unicode_to_utf8
}
?>