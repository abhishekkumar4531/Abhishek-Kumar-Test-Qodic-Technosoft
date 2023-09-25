/**
 * This is responsible for changing the role of an user by Super-Admin.
 * When admin select role from dropdown then on change this function will be
 * execute and communicate with php function where data will be update. 
 */
$(document).on('change','.userRole',function(e){
  var id = $(this).data("user-id");
  var selectRole = $(this).val();

  $.ajax({
    type: "POST",
    url: "/adminUpdate",
    data: {userId : id, newRole : selectRole},
    dataType: 'json',
    success: function(data) {
      if(data.status) {
        alert("Role updated!");
      }
      else {
        alert("Error");
      }
    }
  });
});

/**
 * This is responsible for remove an user by Super-Admin.
 * When admin click on delete icon this function will be execute and communicate
 * with php function where using user will be remove from database. 
 */
$(document).on('click','.deleteUser',function(e){
  prompt("Are you sure?");
  var id = $(this).data("user-id");

  $.ajax({
    type: "POST",
    url: "/deleteUser",
    data: {userId : id},
    dataType: 'json',
    success: function(data) {
      if(data.status) {
        alert("Thank you");
      }
      else {
        alert("Erro");
      }
    }
  });
});

/**
 * This is responsible for Hide/Show the Add user form for Super-Admin.
 */
$(document).ready(function() {
  $('.addUser').hide();
  $(document).on('click', '.addNewUser', function(e) {
    var addNew = $('.addUser');
    addNew.toggle();
  });
});
