<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle">All <?php echo $title;?></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <div class="pageBody">
       <div class="pageFilter clearfix">
        <div class="pageFilter-search">
          <input class="form-control" type="text" placeholder="Search" name="search_text" id="search_text"  />
          <button>Go</button>
        </div>
      </div>
      <table id="ajax_table">
        <thead>
          <tr>
            <th>User Name</th>
            <th>Joined On</th>
            <th class="text-center">Samples Obtained</th>
            <th class="text-center">Samples Reviewed</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="tbody">
      </tbody>
  </table>
  <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val="0">Load More</a></div> 
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
<script type="text/javascript">
  (function($) {
    'use strict';

				// FORM SUBMIT
		$("#login-form").validate({
		errorElement: 'small',
		submitHandler: function() {
		$('#startConfetti').trigger('click');
		}
		});

		// FORM SUBMIT
		$("#forgot-form").validate({
		errorElement: 'small',
		submitHandler: function() {
		$('#forgotModal').modal('hide');
		}
		});

      })(jQuery);
      $(document).ready(function(){
        getUsers(0);
         $("#search_text").on("keyup", function() {
          var value = $(this).val().toLowerCase();
           $("#tbody").html('');
          	getUsers(0,value);
          	$('#load_more').data('val', ($('#load_more').data('val')+1));
        });
        $("#load_more").click(function(e){
          e.preventDefault();
          var page = $(this).data('val');
          var search_text=$("#search_text").val();
          getUsers(page,search_text);
        });
      });
      var getUsers = function(page,search_text=''){
        $("#loader").show();
        // alert(page+','+search_text);
        $.ajax({
          url:"<?php echo base_url() ?>getUsers/"+page,
          async:false,
          type:'POST',
          dataType: "json",
          data: {'search_text':search_text}
        }).done(function(response){
    		console.log(response.totalRecords);
	    if(response.totalRecords < 10 || response.users=='')
	      	$("#divLoadMore").hide();
	    	$("#ajax_table").append(response.users);
	    	$('#load_more').data('val', ($('#load_more').data('val')+1));
	    scroll();
  		});
      };
      var scroll  = function(){
        $('html, body').animate({
          scrollTop: $('#load_more').offset().top
        }, 1000);
      };
    </script>
  </body>
  </html>
