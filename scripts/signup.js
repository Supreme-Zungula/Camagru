function valid_name(firstname)
{
	var regex = /[A-Za-z]+/;
	if (firstname.match(regex))
		return true;
	else
		return false;
}

function validateForm()
{
	var firstname = document.forms['signUp_form']['firstname'].value;
	var lastname = document.forms['signUp_form']['lastname'].value;
	var username = document.forms['signUp_form']['username'].value;
	var email = document.forms['signUp_form']['email'].value;
	var password = document.forms['signUp_form']['password'].value;
	var confirmPassword = document.forms['signUp_form']['confirmPassword'].value;
	alert(firstname);
	if (valid_name(firstname) == 'INVALID')
		{document.getElementsByName('firstname').innerHTML("Name can only have letters");
		return false;
	}

	if (valid_name(lastname) == 'INVALID')
		document.getElementsByName('lastname').innerHTML("This field needs letters only");
	if (valid_name(username) == 'INVALID')
		document.getElementsByName('username').innerHTML("This field can have letters"); 
}