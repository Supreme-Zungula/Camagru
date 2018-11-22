var new_pass = document.getElementsByName('new_password');
    var confirm_new = document.getElementsByName('confirm_password');

    if (new_pass && confirm_new){
        function check_pass(){
            if (new_pass != confirm_new){
                confirm_new.setCustomValidity = 'Password Donot Match!';
            }
            else{
                confirm_new.setCustomValidity('');
            }
        }
    }
    new_pass.onchange = check_pass();
    confirm_new.onkeyup = check_pass();