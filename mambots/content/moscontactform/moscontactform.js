var mosContactFormCode = "";

function mosContactFormSubmit(path){
	mosContactFormClearError();
	
	var name = $('moscontactform_name').value;
	var email = $('moscontactform_email').value;
	var text = $('moscontactform_text').value;
	var contact = $('moscontactform_contact').value;
	var proxyURL = path + "moscontactform_submit.php?";
	var loaderIMG = document.createElement('IMG');
	loaderIMG.src = path + "loader.gif";
	loaderIMG.style.position = "relative";
	loaderIMG.style.left = "50%";
	
	if(!mosContactFormValidateEmail(email))
		return false;
	
	if(name == ""){
		mosContactFormShowError($('moscontactform_name'), "Inserisci il tuo nome!");
		return false;
	}
	
	if(email == ""){
		mosContactFormShowError($('moscontactform_email'), "Inserisci il tuo indirizzo e-mail!");
		return false;
	}

	if(text == ""){
		mosContactFormShowError($('moscontactform_text'), "Inserisci il tuo messaggio!");
		return false;
	}

	var now = new Date;
	var time = now.getFullYear() + "" + now.getMonth() + "" + now.getDate() + "" + now.getHours() + "" + now.getMinutes() + "" + now.getMilliseconds();
	
	mosContactFormCode = $('moscontactformholder').innerHTML
	$('moscontactformholder').innerHTML = "";
	$('moscontactformholder').appendChild(loaderIMG);
	
	var params = "name=" + name + "&email=" + email + "&text=" + text + "&contact=" + contact;
	
	var ajaxObj = new Ajax.Request(
		proxyURL, 
				{	method		: 'get',
					parameters	: params + "&time=" + time,
					onComplete	: mosContactFormSubmitComplete,
					on404		: mosContactFormNotFoundError,
					on500		: mosContactFormInternalError
				});
}

function mosContactFormNotFoundError(){
	$('moscontactformholder').innerHTML = "Si è verificato un errore di configurazione! Il form handler non è stato trovato. (Errore 404) ";
	setTimeout("mosContactFormReplaceCode()", 5000);
}

function mosContactFormInternalError(){
	$('moscontactformholder').innerHTML = "Si è verificato un errore durante l'invio dei dati del form! (Errore 500) ";
	setTimeout("mosContactFormReplaceCode()", 5000);
}

function mosContactFormSubmitComplete(resp){
	var text = resp.responseText;
	$('moscontactformholder').innerHTML = text.replace(/<(.[\>]*?)>/gi, "");	
	setTimeout("mosContactFormReplaceCode()", 5000);
}

function mosContactFormReplaceCode(){
	$('moscontactformholder').innerHTML = mosContactFormCode;	
}

function mosContactFormClearError(){
	var errors = document.getElementsByClassName("moscontactformerror");
	for(var n = 0, len = errors.length; n < len; n++){
		errors[n].parentNode.removeChild(errors[n]);
	}
}
		
function mosContactFormShowError(item, message){
	var errorDiv = document.createElement('DIV');
	errorDiv.className = "moscontactformerror";
	errorDiv.innerHTML = message;
	item.parentNode.insertBefore(errorDiv, item);
	new Effect.Pulsate(errorDiv);
}
	
function mosContactFormValidateEmail(val){
	if(!val) return true;

	var at="@"
	var dot="."
	var lat=val.indexOf(at)
	var lstr=val.length
	var ldot=val.indexOf(dot)
	if (val.indexOf(at)==-1){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: manca il carattere @!")
	   return false;
	}

	if (val.indexOf(at)==-1 || val.indexOf(at)==0 || val.indexOf(at)==lstr){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail");
	   return false;
	}

	if (val.indexOf(dot)==-1 || val.indexOf(dot)==0 || val.indexOf(dot)==lstr){
	    mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: dominio non valido!");
	    return false;
	}

	if (val.indexOf(at,(lat+1))!=-1){
	    mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: controlla i caratteri '@'");
	    return false;
	}

	if (val.substring(val.length-1) == dot){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: controlla i caratteri '.'");
	   return false;
	}

	if (val.substring(lat-1,lat)==dot || val.substring(lat+1,lat+2)==dot){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: dominio non valido!");
	   return false;
	}

	if (val.indexOf(dot,(lat+2))==-1){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: dominio non valido!");
	   return false;
	}
		
	if (val.indexOf(" ")!=-1){
	   mosContactFormShowError($('moscontactform_email'), "Invalid E-mail: non sono consentiti spazi!");
	   return false;
	}
				
	return true;
}
