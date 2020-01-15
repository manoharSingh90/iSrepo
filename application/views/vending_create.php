<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a href="<?php echo base_url('campaign_detail_samples/'.$brand_id.'/'.$campaign_id);?>" class="text-white mr-2">Back</a> | <span class="ml-2">Add Vending Machine</span></h2>
     <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
  </div>

    <?php 
    $attributes = array('id' => 'vendingMachine-form');
    echo form_open('admin/vendingMachine/add', $attributes);?>
  <div class="createInfo mt-1">
    <input type="hidden" name="brand_id" value="<?php echo $brand_id;?>">
    <input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>">
    <h2>Basic Details</h2>
    <div class="appendVendlist">
      <div class="pt-2">
        <div class="row">
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm mb-0">Location Name</label>
            <input type="text" name="location_name[0]" class="form-control" required/>
          </div>
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm mb-0">Vending Machine Code</label>
            <input type="text" autocomplete="off" name="vending_machine_code[0]" id="machine_code_0" class="form-control checkCode" onblur="checkCode(this.value,this.id)" />
            <small class="error"></small>
            <input id="vMachineCheck_0" type="checkbox" class="machineCode" data-msg-required="This field is required." name="vendingMachineName[0]" required="required" style="height: 0; width: 0; opacity: 100%; margin: 0; padding: 0; outline: none; position: absolute;" />
          </div>
        </div>
      </div>
      <div class="pt-3 pb-1">
        <label class="col-form-label-sm mb-3">Address</label>
        <div class="row align-items-center">
          <div class="col-12 col-md-4 pb-4">
            <input type="text" name="location_address[0]" class="form-control" placeholder="Line 1" required/>
          </div>
          <div class="col-12 col-md-4 pb-4">
            <input type="text" name="location_address2[0]" class="form-control" placeholder="Line 2"/>
          </div>
          <div class="col-12 col-md-4 pb-4">           
           <input type="text" name="location_address3[0]" class="form-control" placeholder="Line 3"/>           
          </div>
          <div class="col-12 col-md-4 pb-4">           
            <select class="form-control singleselect fl_input" id="countrySelect_0" name="country[0]" placeholder="Select" onchange="getcity(this.value,'0')" required>
              <option value="">Select Country</option>
              <?php if($country):
                foreach ($country as $value) : ?>
                        <option value="<?php echo $value->id;?>"><?php echo $value->country_name;?></option>
                <?php 
                endforeach;
              endif;
              ?>
            </select>
          </div>
          <div class="col-12 col-md-4 pb-4">
          <!--   <input type="text" name="city[0]" class="form-control" placeholder="City/District/Town" required/> -->
             <select class="form-control singleselect fl_input" id="citySelect_0"  name="city[0]"  required>
                    <option value="cityType">Select City</option>
                  </select>
          </div>
          <div class="col-12 col-md-4 pb-4">
            <input type="number" name="postal_code[0]" class="form-control" placeholder="Postal Code" maxlength="6" minlength="6" required/>
          </div>
          <div class="col-12 col-md-4 pb-4">
            <input type="text" name="landmark[0]" class="form-control" placeholder="Landmark"/>
          </div>
        </div>
      </div>
      <div class="pt-4 pb-2">
        <label class="col-form-label-sm mb-3">Coordinates</label>
        <div class="row align-items-center">
          <div class="col-12 col-md-4 ">
            <input type="number" name="vend_lat[0]" class="form-control" placeholder="Latitude" min="-90" max="90" step="any" title="The latitude must be between -90.0 and 90.0" required/>
          </div>
          <div class="col-12 col-md-4">
            <input type="number" name="vend_long[0]" class="form-control" placeholder="Longitude" min="-180" max="180" step="any" title="The longitude must be between -180.0 and 180.0" required/>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="pt-1 pb-3"> <a href="#" class="text-link font-weight-bold adVendClick">+ Add Another Vending Machine</a> </div>
    <div class="pt-2 pb-2 text-right">
      <button class="btn btn-link mr-3">Cancel</button>
      <button class="btn btn-style" id="create">Create</button>
    </div>
  </div>
  <?php echo form_close();?>
</div>

<script type="text/javascript">
(function($) {
    'use strict';


     // FORM SUBMIT
        $("#vendingMachine-form").validate({
                errorElement: 'small',
                submitHandler: function() {
                  $("#create").attr("disabled", true).addClass("loading");
                  $('#startConfetti').trigger('click');
                  $('.checkCode').each(function() {
                  var validate = checkCode(this.value,this.id);                  
                });                
                  var user = $('#vendingMachine-form').serialize();
                   $.ajax({  
                      type: "POST",  
                      url:  "<?php echo base_url(); ?>" + "admin/vendingMachine/add",  
                      data: user,  
                      cache: false,  
                      success: function(result){  
                        if(result>0){  
                             window.location.replace("<?php echo base_url('campaign_detail_samples/'.$brand_id.'/'.$campaign_id.'');?>");  
                        }  
                        else {
                          $("#create").attr("disabled", false).removeClass("loading");
                        }
                      }  
                      });  
              }
        });
        var wrapper1         = $(".appendVendlist"); 
        $(document).on("click",".adVendClick",function(e) {
          e.preventDefault();
          var count = $('.appendVendlist').length;
          var countActiveDiv = $('.camDiv:visible').length+1;
          var newQuestion = '<div class="appendVendlist"><div class="pt-2"><hr><div class="row align-items-center "><div class="col-12 col-md-4"> <label class="col-form-label-sm mb-0">Location Name</label> <input type="text" name="location_name['+count+']" class="form-control" required/></div><div class="col-12 col-md-4"><label class="col-form-label-sm mb-0">Vending Machine Code</label><input type="text" name="vending_machine_code['+count+']" id="machine_code_'+count+'" class="form-control checkCode" onblur="checkCode(this.value,this.id)" /><small class="error"></small><input id="vMachineCheck_'+count+'" class="machineCode" type="checkbox" data-msg-required="This field is required." name="vendingMachineName['+count+']" required="required" style="height: 0; width: 0; opacity: 0; margin: 0; padding: 0; outline: none; position: absolute;" /></div></div></div><div class="pt-4 pb-1"> <label class="col-form-label-sm mb-3">Address</label><div class="row align-items-center"><div class="col-12 col-md-4 pb-4"> <input type="text" name="location_address['+count+']" class="form-control" placeholder="Line 1" required/></div><div class="col-12 col-md-4 pb-4"> <input type="text" name="location_address2['+count+']" class="form-control" placeholder="Line 2"/></div><div class="col-12 col-md-4 pb-4"><input type="text" name="location_address3['+count+']" class="form-control" placeholder="Line 3"/></div><div class="col-12 col-md-4 pb-4"> <select class="form-control singleselect fl_input" id="countrySelect_'+count+'" name="country['+count+']" placeholder="Select" onchange="getcity(this.value,'+count+')" required><option value="">Select Country</option> <?php if($country): foreach ($country as $value) : ?><option value="<?php echo $value->id;?>"><?php echo $value->country_name;?></option> <?php endforeach; endif; ?> </select></div><div class="col-12 col-md-4 pb-4"> <select class="form-control singleselect fl_input" id="citySelect_'+count+'" name="city['+count+']" required><option value="cityType"></option> </select></div><div class="col-12 col-md-4 pb-4"> <input type="number" name="postal_code['+count+']" class="form-control" placeholder="Postal Code" required/></div><div class="col-12 col-md-4 pb-4"> <input type="text" name="landmark['+count+']" class="form-control" placeholder="Landmark"/></div></div></div><div class="pt-4 pb-2"> <label class="col-form-label-sm mb-3">Coordinates</label><div class="row align-items-center"><div class="col-12 col-md-4 "> <input type="number" name="vend_lat['+count+']" class="form-control" placeholder="Latitude" min="-90" max="90" step="any" title="The latitude must be between -90.0 and 90.0" required/></div><div class="col-12 col-md-4"> <input type="number" name="vend_long['+count+']" class="form-control" placeholder="Longitude" min="-180" max="180" step="any" title="The longitude must be between -180.0 and 180.0" required/></div></div></div></div>';
          $(wrapper1).append(newQuestion);
          //var count = count+1;
        });
})(jQuery);
function getcity(country_id,id)
{
  $.post("<?php echo base_url('admin/vendingMachine/getCity');?>",{country_id:country_id},
  function(html){
  $("#citySelect_"+id).html(html);

});
}
function checkCode(vmcode,id){ 
  var mk_err = 0;  
  if(vmcode ==''){
    mk_err = 1;
  }
  var mc_id = id.split("_");
  mc_id = mc_id[2];
  //$('#vMachineCheck'+mc_id).attr('data-msg-required','This field is required.');

  if(mk_err == 0){
        $.ajax({
          type:"POST",
          url:"<?php echo base_url('admin/vendingMachine/checkCode');?>",
          data:{vmcode:vmcode},
          dataType:'json',
          success:function(rs){           
            if(rs.type =='error'){
              $('#vMachineCheck_'+mc_id).attr('data-msg-required','This machine code is already exists');
              $('#vMachineCheck_'+mc_id).prop('checked',false);
              $('#'+id).next('small').text('This machine code is already exists');              
            }else{              
             $('#vMachineCheck_'+mc_id).prop('checked',true);
            }
          },
          error:function(er){
            //
          }

        })
      }
}



$('#CompanyCompanyName').on('blur',function(){
      var hostCmpName = $(this).val();
      var mk_err = 0;
      if(hostCmpName ==''){
        mk_err = 1;
        $('#company_name_error').text('required');
      }
      if(mk_err == 0){
        $.ajax({
          type:"POST",
          url:url+'SuperAdmins/check_host_name',
          data:{hostname:hostCmpName},
          dataType:'json',
          success:function(rs){           
            if(rs.type == 'error'){
              $('#company_name_error').text(rs.msg);
              setTimeout(function(){
                        $('#CompanyCompanyName').val('');
                   },2000);
            }if(rs.type == 'success'){
              $('#company_name_error').text('');
            }
          },
          error:function(er){
            //
          }

        })
      }
    })
/*function numbersonly(e){
    var unicode=e.charCode? e.charCode : e.keyCode
    if (unicode!=8){ //if the key isn't the backspace key (which we should allow)
        if (unicode<48||unicode>57) //if not a number
            return false //disable key press
    }
}*/


</script>
</body>
</html>
