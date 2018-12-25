var DeBug=true;

function ById(IDName){
	if(!document.getElementById(IDName)){
		if(DeBug){
			console.log('%cById(\''+IDName+'\') Error','color:red');
		}
		return false;
	}
	return document.getElementById(IDName);
}
function AutoFontSize(){
	var PageHeight=window.screen.availHeight;
	var Html=document.getElementsByTagName("html")[0];
	var PageWidth=document.body.clientWidth;
	if(PageWidth<960){
		var FontScale=PageWidth/414;
		document.getElementsByTagName("html")[0].style.height=PageHeight+'px';
		document.body.style.height=PageHeight+'px';
		Html.style.fontSize=6.25*FontScale+'%';
		Mobile();
	}
	else{
		var FontScale=PageWidth/1920;
		Html.style.fontSize=6.25*FontScale+'%';
	}
}
function Jump(Href){
	window.location.href=Href;
}
function Mobile(){
}