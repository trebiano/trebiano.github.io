<?php

global $do_gzip_compress;

/**
 * Initialise GZIP
 */
function initGzip() {
	global $do_gzip_compress;

	$do_gzip_compress = FALSE;
	if (true) {
		$phpver 	= phpversion();
		$useragent 	= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$canZip 	= isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING']: '';

		$gzip_check 	= 0;
		$zlib_check 	= 0;
		$gz_check		= 0;
		$zlibO_check	= 0;
		$sid_check		= 0;
		if ( strpos( $canZip, 'gzip' ) !== false) {
			$gzip_check = 1;
		}
		if ( extension_loaded( 'zlib' ) ) {
			$zlib_check = 1;
		}
		if ( function_exists('ob_gzhandler') ) {
			$gz_check = 1;
		}
		if ( ini_get('zlib.output_compression') ) {
			$zlibO_check = 1;
		}
		if ( ini_get('session.use_trans_sid') ) {
			$sid_check = 1;
		}

		if ( $phpver >= '4.0.4pl1' && ( strpos($useragent,'compatible') !== false || strpos($useragent,'Gecko')	!== false ) ) {
			// Check for gzip header or northon internet securities or session.use_trans_sid
			if ( ( $gzip_check || isset( $_SERVER['---------------']) ) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check ) {
				// You cannot specify additional output handlers if
				// zlib.output_compression is activated here
				ob_start( 'ob_gzhandler' );
				return;
			}
		} else if ( $phpver > '4.0' ) {
			if ( $gzip_check ) {
				if ( $zlib_check ) {
					$do_gzip_compress = TRUE;
					ob_start();
					ob_implicit_flush(0);

					header( 'Content-Encoding: gzip' );
					return;
				}
			}
		}
	}
	ob_start();
}

/**
* Perform GZIP
*/
function doGzip() {
	global $do_gzip_compress;
	if ( $do_gzip_compress ) {
		/**
		*Borrowed from php.net!
		*/
		$gzip_contents = ob_get_contents();
		ob_end_clean();

		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);

		$gzip_contents = gzcompress($gzip_contents, 9);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $gzip_contents;
		echo pack('V', $gzip_crc);
		echo pack('V', $gzip_size);
	} else {
		ob_end_flush();
	}
}

if(@$_GET['compress'])
	initGzip();
 
header("Content-type: text/javascript; charset: UTF-8"); 
header("Cache-Control: public"); 
$offset = 60 * 60 ; 
$ExpStr = "Expires: " .  
gmdate("D, d M Y H:i:s", 
time() + $offset) . " GMT"; 
header($ExpStr); 

?>

function Jax()
{
	
	var loadingTimeout = 400;
	var iframe;
	
	this.loadingFunction = function(){};
	this.doneLoadingFunction = function(){};
	

	
	this.stringify = function(arg){
	    var c, i, l, o, u, v;
	
	    switch (typeof arg) {
	    case 'object':
	    	//alert('obj');
	        if (arg) {
	            if (arg.constructor == Array) {
	                o = '';
	                for (i = 0; i < arg.length; ++i) {
	                    v = this.stringify(arg[i]);
	                    if (o && (v !== u)) {
	                        o += ',';
	                    }
	                    if (v !== u) {
	                        o += v;
	                    } 
	                }
	                return '[' + o + ']';
	            } else if (typeof arg.toString != 'undefined') {
	                o = '';
	                for (i in arg) {
	                    v = this.stringify(arg[i]);
	                    if (v !== u) {
	                        if (o) {
	                            o += ',';
	                        }
	                        o += this.stringify(i) + ':' + v;
	                    }
	                }
	                return '{' + o + '}';
	            } else {
	                return;
	            }
	        }
	        //return 'null';
	        return '';
	    case 'unknown':
	    case 'undefined':
	    case 'function':
	        return u;
	    case 'string':
	        l = arg.length;
	        o = '"';
	        for (i = 0; i < l; i += 1) {
	            c = arg.charAt(i);
	            if (c >= ' ') {
	                if (c == '\\' || c == '"') {
	                    o += '\\';
	                }
	                o += c;
	            } else {
	                switch (c) {
	                case '\b':
	                    o += '\\b';
	                    break;
	                case '\f':
	                    o += '\\f';
	                    break;
	                case '\n':
	                    o += '\\n';
	                    break;
	                case '\r':
	                    o += '\\r';
	                    break;
	                case '\t':
	                    o += '\\t';
	                    break;
	                default:
	                    c = c.charCodeAt();
	                    o += '\\u00';
						o += Math.floor(c / 16).toString(16);
						o += (c % 16).toString(16);
	                }
	            }
	        }
	        return o + '"';
	    default:
	        return String(arg);
	    }
	}
	
	/**
	 * Get XMLHttpObject
	 */	 	
	this.getRequestObject = function()
	{
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
		   http_request = new XMLHttpRequest();
		} else if (window.ActiveXObject) { // IE
		    var msxmlhttp = new Array(
					'Msxml2.XMLHTTP.4.0',
					'Msxml2.XMLHTTP.3.0',
					'Msxml2.XMLHTTP',
					'Microsoft.XMLHTTP');
		    
		    for (var i = 0; i < msxmlhttp.length; i++) {
				try {
					http_request = new ActiveXObject(msxmlhttp[i]);
				} catch (e) {
					http_request = null;
				}
			}
		}
		
		if (!http_request) {
		   alert('Unfortunatelly you browser doesn\'t support this feature.');
		   return false;
		}
		
		return http_request;
	}

	/**
	 * xajax.$() is shorthand for document.getElementById()
	 */
	this.$ = function(sId)
	{
		if (!sId) {
			return null;
		}
		var returnObj = document.getElementById(sId);
		if (!returnObj && document.all) {
			returnObj = document.all[sId];
		}
		
		return returnObj;
	}
	
	

   this.addEvent = function ( obj, type, fn ) {
     if ( obj.attachEvent ) {
       obj['e'+type+fn] = fn;
       obj[type+fn] = function(){obj['e'+type+fn]( window.event );}
       obj.attachEvent( 'on'+type, obj[type+fn] );
     } else{
       obj.addEventListener( type, fn, false );}
   }
   
   this.removeEvent = function ( obj, type, fn ) {
     if ( obj.detachEvent ) {
       obj.detachEvent( 'on'+type, obj[type+fn] );
       obj[type+fn] = null;
     } else{
       obj.removeEventListener( type, fn, false );}
   }


	
	this.submitITask = function(comName, func, postData, responseFunc){
		var xmlReq = this.buildXmlReq(comName, func, postData, responseFunc, true);
		this.loadingFunction();
	    if(!this.iframe){
			this.iframe = document.createElement('iframe');
			this.iframe.setAttribute("id", 'ajaxIframe');
			this.iframe.setAttribute("height", 0);
			this.iframe.setAttribute("width", 0);
			this.iframe.setAttribute("border", 0);
			this.iframe.style.visibility = 'hidden';
			document.body.appendChild(this.iframe);
			this.iframe.src = xmlReq;
		} else {
			this.iframe.src = xmlReq;
		}
	}
	
	this.extractIFrameBody = function(iFrameEl) {
	  var doc = null;
	  if (iFrameEl.contentDocument) { // For NS6
	    doc = iFrameEl.contentDocument; 
	  } else if (iFrameEl.contentWindow) { // For IE5.5 and IE6
	    doc = iFrameEl.contentWindow.document;
	  } else if (iFrameEl.document) { // For IE5
	    doc = iFrameEl.document;
	  } else {
	    alert("Error: could not find sumiFrame document");
	    return null;
	  }
	  return doc.body;
	
	}
	
	this.buildXmlReq = function(comName, func, postData, responseFunc, iframe){
		var xmlReq = '';
		if(iframe){
			xmlReq += '?';}
		else{
			xmlReq += '&';}
			
	    xmlReq += 'option='+ comName;
	    xmlReq += '&no_html=1';
	    xmlReq += '&task=azrul_ajax';
	    xmlReq += '&func=' + func; 
	    if(postData){
	        xmlReq += "&" + postData;
	    }
	    
	    return xmlReq;
	}
	
	/**
	 * Sumbit ajax task
	 */
	this.submitTask = function(comName, func, postData, responseFunc){
	
	    var xmlhttp =  this.getRequestObject();
	    var targetUrl = jax_live_site;
	    
	    xmlhttp.open('POST', targetUrl, true);
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4) {
	            if (xmlhttp.status == 200){
	            	jax.doneLoadingFunction();
	            	jax.processResponse(xmlhttp.responseText);
	            }else {
	                // warning ajax fails
	            }
	        }
	    }
	    
	   	
						
	    var id = 1;
	    var xmlReq = this.buildXmlReq(comName, func, postData, responseFunc);
		
		this.loadingFunction();
	    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    xmlhttp.send(xmlReq);
	}
	
	this.processIResponse = function(){
		jax.doneLoadingFunction();
		var resp = (this.extractIFrameBody(this.iframe).innerHTML);
		resp = resp.replace(/&lt;/g, "<");
		resp = resp.replace(/&gt;/g, ">");
		resp = resp.replace(/&amp;/g, "&");
		resp = resp.replace(/&quot;/g, '"');
		resp = resp.replace(/&#39;/g, "'");
		
		this.processResponse(resp);
	}
	
	/**
	 * Process the json string
	 */	 	
	this.processResponse = function(responseTxt){
		// clean up any previous error
		responseTxt = responseTxt.substring(responseTxt.indexOf("{"));
		var result = eval("(" + responseTxt + ")");
		
		// we now have an array, that contains an array.
		for(var i=0; i<result.length;i++){
		
			var cmd 		= result[i][0];
			var id			= result[i][1];
			var property 	= result[i][2];
			var data 		= result[i][3];

			var objElement = this.$(id);
			
			switch(cmd){
				case 'as': 	// assign or clear
					
					if(objElement){
						data = data.replace(/"/g, "\\\"");
						data = data.replace(/&#123;/g, "{");
						data = data.replace(/&#125;/g, "}");
						eval("objElement."+property+"=\""+ data + "\";");
					}
						
					break;
					
				case 'al':	// alert
					if(data){
						alert(data);}
					break;
				
				case 'ce':
					this.create(id,property, data);
					break;
					
				case 'rm':
					this.remove(id);
					break;
					
				case 'cs':	// call script
					var scr = id + '(';
					if(this.isArray(data)){
						scr += 'data[0]';
						for (var l=1; l<data.length; l++) {
							scr += ',data['+l+']';
						}
					} else {
						scr += 'data';
					}
					scr += ');';
					eval(scr);
					break;
				
				default:
					alert("Unknow command: " + cmd);
			}
		}
		
		delete responseTxt;
	}
	
	/**
	 *
	 */	 	
	this.isArray =  function(obj) { // this works
		if(obj){
			return obj.constructor == Array;
		}
		return false;
	}
	
	this.buildCall = function(comName, sFunction){
	}
	this.icall = function(comName, sFunction){
		var arg = "";
		if(arguments.length > 2){
			for(var i=2; i < arguments.length; i++){
				var a = arguments[i];
				if(this.isArray(a)){
					arg += "arg" + i + "=" + this.stringify(a) + "&";
				}else if(typeof a =="string"){
					a = a.replace(/"/g, "&quot;");
					
					// Encode the '{' and '}' characters
	    			a = a.replace(/{/g, "&#123;");
					a = a.replace(/}/g, "&#125;");
		
					var t = new Array('_d_', encodeURIComponent(a));
					arg += "arg" + i + "=" + this.stringify(t) + "&";
				} else {
					var t = new Array('_d_', encodeURIComponent(a));
					arg += "arg" + i + "=" + this.stringify(t) + "&";
				}
			}
		}
		
		this.submitITask(comName, sFunction, arg);
	}
	
	/**
	 * Function call to PHP function
	 */
	this.call = function(comName, sFunction){
		
		var arg = "";
		if(arguments.length > 2){
			for(var i=2; i < arguments.length; i++){
				var a = arguments[i];
				if(this.isArray(a)){
					arg += "arg" + i + "=" + this.stringify(a) + "&";
				}else if(typeof a =="string"){
					a = a.replace(/"/g, "&quot;");
					
					// Encode the '{' and '}' characters
	    			a = a.replace(/{/g, "&#123;");
					a = a.replace(/}/g, "&#125;");
		
					var t = new Array('_d_', encodeURIComponent(a));
					arg += "arg" + i + "=" + this.stringify(t) + "&";
				} else {
					var t = new Array('_d_', encodeURIComponent(a));
					arg += "arg" + i + "=" + this.stringify(t) + "&";
				}
			}
		}
		
		this.submitTask(comName, sFunction, arg);
	}
	
	this.create = function(sParentId, sTag, sId){
		var objParent = this.$(sParentId);
		objElement = document.createElement(sTag);
		objElement.setAttribute('id',sId);
		if (objParent){
			objParent.appendChild(objElement);}
	}
	
	this.remove = function(sId){
		objElement = this.$(sId);
		if (objElement && objElement.parentNode && objElement.parentNode.removeChild)
		{
			objElement.parentNode.removeChild(objElement);
		}
	}
	
	/**
	 * Return an array of data within the form object
	 */	 	
	this.getFormValues = function(frm){
		var objForm;
		objForm = this.$(frm);

		var postData = new Array();
		if (objForm && objForm.tagName == 'FORM'){
			var formElements = objForm.elements;
			for( var i=0; i < formElements.length; i++){
				if (!formElements[i].name){
					continue;}
				if (formElements[i].type && (formElements[i].type == 'radio' || formElements[i].type == 'checkbox') && formElements[i].checked == false){
					continue;}
				var name = formElements[i].name;
				if (name){
					if(formElements[i].type=='select-multiple'){
						postData[i] = new Array();
						for (var j = 0; j < formElements[i].length; j++){
							if (formElements[i].options[j].selected === true){
								var value = formElements[i].options[j].value;
								value = value.replace(/"/g, "&quot;");
								
								value = value.replace(/{/g, "&#123;");
								value = value.replace(/}/g, "&#125;");
						
								postData[i][j] = new Array(name, encodeURIComponent(value));
							
							}
						}
					} else {
						var value = formElements[i].value;
						// Encode the '{' and '}' characters
	    				value = value.replace(/{/g, "&#123;");
						value = value.replace(/}/g, "&#125;");
						
						value = value.replace(/"/g, "&quot;");
						postData[i] = new Array(name, encodeURIComponent(value));
					}
				} 
			}
		}
		
		return postData;
	}	
}

function jax_iresponse(){
	jax.processIResponse();
}
var jax = new Jax();

<?php

if(@$_GET['compress'])
	doGzip();

