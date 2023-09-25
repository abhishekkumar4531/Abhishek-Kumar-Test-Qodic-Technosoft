//check_valid variable is type of RegeX variable and used for user name validations.
//simlarlly, check_phone for user phone number and check_email for user email.
var check_valid = /^[A-Za-z]+$/;
var check_phone = /^(\+91)[0-9]{10}$/;
var check_email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
var check_pwd = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;

/**
 * checkFname is responsible for checking the validations of user's first name feild.
 * When user entered their first name then onblur this function will be execute.
 * First it will fetch the what user entered and then call a function for checking
 * the validations of entered value.
 * If user entered valid then ok otherwise it will show a error message.
 */
function checkFname() {
  var userName = document.getElementById('firstName').value;
  if(!(userName.match(this.check_valid))) {
    document.getElementById("invalid_fname").innerHTML = `Enter only alphabets`;
    document.getElementById("submitButton").disabled = true;
  }
  else {
    document.getElementById("invalid_fname").innerHTML = '';
    document.getElementById("submitButton").disabled = false;
  }
}

/**
 * checkLname is responsible for checking the validations of user's last name feild.
 * When user entered their last name then onblur this function will be execute.
 * First it will fetch the what user entered and then call a function for checking
 * the validations of entered value.
 * If user entered valid then ok otherwise it will show a error message.
 */
function checkLname() {
  var userName = document.getElementById('lastName').value;
  if(!(userName.match(this.check_valid))) {
    document.getElementById("invalid_lname").innerHTML = `Enter only alphabets`;
    document.getElementById("submitButton").disabled = true;
  }
  else {
    document.getElementById("invalid_lname").innerHTML = '';
    document.getElementById("submitButton").disabled = false;
  }
}

/**
 * checkPhoneNo is responsible for checking the validations of user's mobile number feild.
 * When user entered their mobile number then onblur this function will be execute.
 * First it will fetch the what user entered and then call a function for checking
 * the validations of entered value.
 * If user entered valid then ok otherwise it will show a error message.
 */
function checkPhoneNo() {
  var userPhone = document.getElementById('userPhone').value;
  if(!(userPhone.match(this.check_phone))) {
    document.getElementById("invalid_phone").innerText = `Enter valid mobile number`;
    document.getElementById("submitButton").disabled = true;
  }
  else {
    document.getElementById("invalid_phone").innerText = '';
    document.getElementById("submitButton").disabled = false;
  }
}

/**
 * checkEmailStatus is responsible for checking the validations of user's email feild.
 * When user entered their email then onblur this function will be execute.
 * First it will fetch the what user entered and then call a function for checking
 * the validations of entered value.
 * If user entered valid then show a success otherwise it will show a error message.
 */
function checkEmailStatus() {
  var userEmail = document.getElementById('userEmail').value;
  if(!(userEmail.match(this.check_email))) {
    document.getElementById("email_status").innerText = `Enter valid email`;
    document.getElementById("email_success").innerText = ``;
    document.getElementById("submitButton").disabled = true;
  }
  else {
    document.getElementById("email_status").innerText = ``;
    document.getElementById("email_success").innerText = `Valid email`;
    document.getElementById("submitButton").disabled = false;
  }
}

/**
 * checkPasswordStatus is responsible for checking the validations of user's password feild.
 * When user entered their password then onblur this function will be execute.
 * First it will fetch what user entered and then call a function for checking
 * the validations of entered value.
 * If user entered valid then show success message otherwise it will show a error message.
 */
function checkPasswordStatus() {
  var userPwd = document.getElementById('userPassword').value;
  if(!(userPwd.match(this.check_pwd))) {
    document.getElementById("pwd_success").innerText = ``;
    document.getElementById("pwd_status").innerText = `Enter valid password`;
    document.getElementById("submitButton").disabled = true;
  }
  else {
    document.getElementById("pwd_status").innerText = ``;
    document.getElementById("pwd_success").innerText = `Valid password`;
    document.getElementById("submitButton").disabled = false;
  }
}

/**
 * onfirmPassword is responsible for comparing the new and confirm password.
 * When user entered their confirm password then onblur this function will be execute.
 * First it will fetch from both the feild what user entered and then call a
 * function for comapring the values.
 * If user entered valid then ok otherwise it will show a error message.
 */
function confirmPassword() {
  var newPwd = document.getElementById('userPassword').value;
  var cnfPwd = document.getElementById('confirmPassword').value;
  if(newPwd === cnfPwd){
    document.getElementById("cnfPwd_status").innerText = ``;
    document.getElementById("submitButton").disabled = false;
  }
  else {
    document.getElementById("cnfPwd_status").innerText = `Please enter same password`;
    document.getElementById("submitButton").disabled = true;
  }
}
