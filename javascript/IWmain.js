function toggleCheckAll(chkbox){
	for (var i=0 ;i < document.forms["form1"].elements.length; i++){
		var elemento = document.forms[0].elements[i];
		if (elemento.type == "checkbox"){
			elemento.checked = chkbox.checked;
		}
	} 
}

function stateCheckAll(chkbox){
	if (document.getElementById('checkall').checked && chkbox === false){
		document.getElementById('checkall').checked = chkbox;
	}	
}

function reloadNewsBlock(){
	var pars = "module=IWmain&func=reloadNewsBlock";
	var myAjax = new Ajax.Request("ajax.php", {
		method: 'get',
		parameters: pars,
		onComplete: reloadNewsBlock_response,
		onFailure: reloadNewsBlock_failure
	});
}

function reloadNewsBlock_response(req){
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}

	var json = pndejsonize(req.responseText);
	Element.update('IWmain_block_news', json.content).innerHTML;
}

function reloadNewsBlock_failure(req){

}


function reloadFlaggedBlock(){
	var pars = "module=IWmain&func=reloadFlaggedBlock";
	var myAjax = new Ajax.Request("ajax.php", {
		method: 'get',
		parameters: pars,
		onComplete: reloadFlaggedBlock_response,
		onFailure: reloadFlaggedBlock_failure
	});
}

function reloadFlaggedBlock_response(req){
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}

	var json = pndejsonize(req.responseText);
	Element.update('IWmain_block_flagged', json.content).innerHTML;
}

function reloadFlaggedBlock_failure(req){

}


function activeGoogleUserAcoountData(){
	var f = document.forms.conf;
	if(f.gCalendarUse.checked){
		$('googleUser').style.display = 'block';
	}else{
		$('googleUser').style.display = 'none';
	}
	
}
