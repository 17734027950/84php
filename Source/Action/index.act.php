<?php
//文件名以 "*.act.php"结尾的文件是用来处理表单数据的，通常通过JavaScript提示处理结果或跳转到处理结果页。这个页面不应该有HTML输出。
//The file whose name ends with "*.act.php" is used to process form data, usually through JavaScript prompt to process the result or jump to the processing result page. This page should not have HTML output.

exit;#
//防止模板代码被运行。这个代码不会存在于生成的文件中。
//To prevent template code from being run. This code will not exist in the generated file.

#$Post=<Receive@Post(FALSE,array('testinput'))>
//调用“Receive”模块中的“Post()”方法，并将返回值存储于“$Post”变量中，你也可以使用其它变量名。请注意，“$Post”是经过安全处理的“$_POST”变量，即“$Post”是安全的，而“$_POST”可能包含恶意数据。因此，无论何种情况都不要直接使用“$_POST”变量。
//Call the "Post ()" method in the "Receive" module and store the return value in the "$Post" variable. You can also use other variable names. Note that "$Post" is a safe processed "$_POST" variable, that is, "$Post" is secure, and "$_POST" may contain malicious data. Therefore, do not use the "$_POST" variable directly in any case.

if(empty($Post['testinput'])){
	die('<script>alert("Empty.");window.location.href="/index.php"</script>');
}
else{
	die('<script>alert("'.$Post['testinput'].'");window.location.href="/index.php"</script>');
}
?>