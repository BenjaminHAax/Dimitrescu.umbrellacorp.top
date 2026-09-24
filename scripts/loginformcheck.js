function hashing()
{
    document.loginform.password.value = sha256(document.loginform.password.value);
}

function loginFormCheck()
{
	if(document.loginform.employeecode.value=="" || document.loginform.employeecode.value.indexOf("-") == -1)
	{
       		alert("You must give an employee code!");
	        return false;
	}
	if(document.loginform.password.value=="")
	{
       		alert("You must give a password!");
	        return false;
	}
}