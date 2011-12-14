//function below added by logan (cailongqun [at] yahoo [dot] com [dot] cn) from www.phpletter.com
function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function selectFile(url)
{
	console.log(url);
  if(url != '' )
  {     
      //window.opener.SetUrl( url ) ;
    window.opener.CKEDITOR.tools.callFunction(getParameterByName("CKEditorFuncNum"), url);  
	console.log(getParameterByName("CKEditorFuncNum"));
	window.close() ;
      
  }else
  {
     alert(noFileSelected);
  }
  return false;
  

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
  return false;
}