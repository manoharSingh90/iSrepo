<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iSamplez</title>
<link rel="shortcut icon" type="image/png" href="<?php echo base_url();?>assets/img/favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
</head>

<body>
<div class="credentialPage">
  <div class="loginForm">
    <h1><img src="<?php echo base_url();?>assets/img/isamplez_logo.png" alt="#"/></h1>
    <h2>Welcome Admin</h2>
   <!--  <form id="login-form"> -->
      <?php 
          $attributes = array('class' => 'text-center', 'id' => 'login-form');
          echo form_open('admin-login', $attributes);?>
        <div id="err_msg" style="display: none">   
          <h2 style="color: red"><div id="msg"> </div></h2>
         </div> 
      <div class="form-group">

        <input type="email" class="form-control" name="email" placeholder="Email" required/>
      </div>
      <div class="form-group pt-1">
        <input type="password" class="form-control" name="password" placeholder="Password" required/>
      </div>
      <div class="form-group text-center pt-1"> <a href="#" class="btn btn-link" data-toggle="modal" data-target="#forgotModal">Forgot Password</a> </div>
      <div class="form-group text-center pt-2 clearfix">
        <button class="btn btn-style" type="submit">Login</button>
      </div>
      <button id="startConfetti" type="button" class="hidden">Start</button>
      <button id="restartConfetti" type="button" class="hidden">Restart</button>
    <?php echo form_close();?>
  </div>
</div>

<!-- FORGOT MODAL -->
<div class="modal fade" id="forgotModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Forgot Password</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="forgotContent pb-2 pt-1">
            <p class="col-form-sublabel text-center pb-3">Please enter your registered email ID to request a password reset:</p>
            <input type="email" class="form-control" placeholder="Email" required/>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link text-dark" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MESSAGE MODAL -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="forgot-formm">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Success!</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="pt-3 pb-0">
            <p class="text-center">Your password has been reset successfully!<br/>
              Your new password has been sent to your primary email address.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-style" type="button" data-dismiss="modal">Done</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPT --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery-validation-master/dist/jquery.validate.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery.confetti.js-master/jquery.confetti.js"></script> 
<script type="text/javascript">
(function($) {
    'use strict';

				// FORM SUBMIT
				$("#login-form").validate({
								errorElement: 'small',
								submitHandler: function() {
									$('#startConfetti').trigger('click');
                  var user = $('#login-form').serialize();
                   $.ajax({  
                      type: "POST",  
                      url:  "<?php echo base_url(); ?>" + "admin-login",  
                      data: user,  
                      cache: false,  
                      success: function(result){  
                          if(result=="success"){  
                            console.log(result);
                            window.location.replace("users-list");  
                          }  
                          else  
                              jQuery("div#err_msg").show();  
                              jQuery("div#msg").html(result);  
                      }  
                      });  
        
								/*	setTimeout(function(){ 
									window.location.replace("admin-login");
									}, 1200);*/
							}
				});

				// FORM SUBMIT
				$("#forgot-form").validate({
								errorElement: 'small',
								submitHandler: function() {
									$('#forgotModal').modal('hide');
									$('#msgModal').modal('show');
								}
				});


			$('#forgotModal').on('hidden.bs.modal', function () {
									$('#forgot-form')[0].reset();;
			});

})(jQuery);
</script>
</body>
</html>
