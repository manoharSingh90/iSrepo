<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle">
      <a class="text-white mr-2" href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>" onClick="history.back();"  value="Back" style="cursor: pointer">Back</a> Create New Campaign</span></h2>
      <div class="float-right pageAction">
        <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a>
      </div>
    </div>
    <div class="pageTabs mt-1 extrawWide">
      <ul>
        <li><a  id="camp" href="<?php echo base_url('create-campaigns/'.$brand_id.'/'.$campaign_id);?>" class="<?php if($this->uri->segment(1)=='create-campaigns') echo "active";?>" >Campaign</a></li>
        <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-samples') echo "active";?>">Samples</a></li>
      <li><a href="<?php if($sampleData=='') echo  "javascript: void(0)" ;else echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-targetAudience') echo "active";?>">Target Audience</a></li>
      <li><a href="<?php if($sampleData=='') echo  "javascript: void(0)" ;else echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-questionnaire') echo "active";?>">Questionnaire</a></li>
      <li class="disabled" ><a href="<?php if(@$qusDataCount==0) echo  "javascript: void(0)" ;else echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-campaign-brand') echo "active";?>" >Brand Details</a></li>
    </ul>
  </div>
  <div class="createInfo">
    <?php 
    $attributes = array('id' => 'samples-form');
    echo form_open('admin/samples/addCampSamples', $attributes);?>
    <div id="err_msg" style="display: none">   
      <h2 style="color: red"><div id="msg"> </div></h2>
    </div> 
    <input type="hidden" name="brand_id" value="<?php echo $brand_id;?>">
    <input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>">
    <div class="pt-2 pb-5">
      <h2>Basic Details</h2>
      <div class="row ">
        <div class="col-12 col-md-3">
          <label class="col-form-label-sm">Start Date</label>
          <input id="startDateInput" type="text" name="start_date" class="form-control dateIcon" autocomplete="off" required onkeydown="return false;" />
        </div>
        <div class="col-12 col-md-3">
          <label class="col-form-label-sm">End Date</label>
          <input id="endDateInput" type="text" name="end_date" class="form-control dateIcon" autocomplete="off" required onkeydown="return false;"/>
        </div>
        <div class="col-12 col-md-3">
          <label class="col-form-label-sm">Total No. of Samples</label>
          <input type="number" name="total_campaign_samples" class="form-control" maxlength="10" data-msg-maxlength="Max limit allowed 10 digit only" required min='1'/>
        </div>
      </div>
    </div>
    <div class="pb-1">
      <table>
        <thead>
          <tr>
            <th class="text-center"> </th>
            <th>Location Name</th>
            <th>Address</th>
            <th>Vending Machine Code</th>
            <!-- <th>Total No. of Samples</th> -->
          </tr>
        </thead>
        <tbody>
          <?php if($vendMachines){
            $count=0;
            foreach ($vendMachines as $key => $value) {
              $checkCampaignEnd = $this->common_model->getLastInsertField(CAMPAIGN_VENDS,'campaign_id',array('vend_machine_id'=>$value->id),'id desc');              
              $campEndDate = $this->common_model->getField(CAMPAIGNS,'end_date',array('id'=>$checkCampaignEnd));
              $countt=$this->common_model->mysqlNumRows(CAMPAIGN_VENDS,'id',array('campaign_id !='=>$campaign_id,'vend_machine_id'=>$value->id));            
              if($campEndDate < date('Y-m-d')){              
                $disable='';
              }/*else if($countt>0){
                $disable='onclick="return false"';
              }*/
              else
                $disable='onclick="return false;"';

            $address=$value->location_address;
            if($value->landmark)
              $address .= ', '.$value->landmark;
            if($value->city_name)
              $address .= ', '.$value->city_name;
            if($value->country_name)
              $address .= ' '.$value->country_name;
            if($value->postal_code)
              $address .= ' '.$value->postal_code;

             ?>
              <tr>
                <td class="text-center"><div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input checkbox" id="custom-Check-<?php echo $count;?>" name="sample_vending_machine_<?php echo $count;?>" value="<?php echo $value->id;?>"  <?php echo $disable;?>>
                  <input type="hidden" name="vending_machine_id[]" value="<?php echo $value->id;?>">
                  <label class="custom-control-label" for="custom-Check-<?php echo $count;?>"></label>
                </div></td>
                <td class="text-dark font-weight-bold"><?php echo $value->location_name;?></td>
                <td><?php echo $address ;?></td> 
                <td class=""><?php echo $value->vending_machine_code;?></td>
                <!-- <td>
                 <input type="text" name="total_samples[<?php echo $count;?>]" id="total_samples-<?php echo $count;?>"  value="" class="form-control" maxlength="7" onkeypress="return numbersonly(event)"></td> -->
              </tr>
              <?php $count++;} } ?>
            </tbody>
          </table>
        </div>
        <div class="pt-2 pb-2 text-right"> <a class="btn btn-link mr-2" href="<?php echo base_url('create-campaigns/'.$brand_id.'/'.$campaign_id);?>">Back</a>
          <button class="btn btn-style" id="next">Next</button>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
    <div class="modal fade" id="ErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content ">
          <div class="modal-header">
            <h5 class="modal-title pt-1 text-primary">Error</h5>
            <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body text-center">
            <p>Please select atleast one vending machine.</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-style" type="button" data-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <footer></footer>

    <!-- SCRIPT --> 
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/jquery-date-range-picker-master/dist/jquery.daterangepicker.min.js"></script>  -->
<script type="text/javascript">
  (function($) {
    'use strict';
       $('.checkbox').change(function(){
       var chekboxIdArr=this.id.split('custom-Check-');
       var $me = $("#total_samples-"+chekboxIdArr['1']);
        if($(this).is(":checked")) {
          $me.attr('required',true).attr('min','1');
      }  else {
        $me.removeAttr('required').removeAttr('min');
      }
    });
    // FORM SUBMIT
    $("#samples-form").validate({
      errorElement: 'small',
      submitHandler: function() {
        var numberOfChecked = $('input:checkbox:checked').length;
        if(numberOfChecked<1){
          $('#ErrorModal').modal();
          return false;
        }
        $("#next").attr("disabled", true);
        $('#startConfetti').trigger('click');
        var user = $('#samples-form').serialize();
        $.ajax({  
          type: "POST",  
          url:  "<?php echo base_url(); ?>" + "admin/samples/addCampSamples",  
          data: user,  
          cache: false,  
          success: function(campaign_id){  
           if(campaign_id>0){  
            console.log(campaign_id);
            window.location.replace("<?php echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>");
            //window.location.replace("<?php //echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>");  
          }  
          else{
            $("#next").attr("disabled", false);
                          /*jQuery("div#err_msg").show();  
                          jQuery("div#msg").html('Error'); */
                        }
                      }  
                    });  
      }
    });

    // DATE PICKER
   // var dateTime = new Date();
   // dateTime = moment(dateTime).format('DD-MM-YYYY');
    var strtdateTime="<?php echo $campData->start_date;?>";
    var dateTime = moment(strtdateTime).format('DD-MM-YYYY');
    var endDateTime="<?php echo $campData->end_date;?>";
    var end_date = moment(endDateTime).format('DD-MM-YYYY');
    $('#endDateInput').attr('data-date', dateTime);

    $('#startDateInput').dateRangePicker({
      format: 'DD-MM-YYYY',
      autoClose: true,
      singleDate: true,
      showTopbar: false,
      singleMonth: true,
      selectForward: true,
      endDate:end_date,
      setValue: function(s) {
        if (!$(this).attr('readonly') && !$(this).is(':disabled') && s != $(this).val()) {
          $(this).val(s);
          $('#endDateInput').attr('data-date', s).val('');
        }
      },
      startDate: dateTime
    });

    $(document).on('focus', '#endDateInput', function(e) {
      var defaultDate = $(this).val();
      var startDate = $(this).attr('data-date');
      if (startDate == '') {
        var startDate = $(this).attr('data-date', dateTime);
      } else {
        var startDate = $(this).attr('data-date');
      }
      $(this).dateRangePicker({
        format: 'DD-MM-YYYY',
        autoClose: true,
        singleDate: true,
        showTopbar: false,
        singleMonth: true,
        selectBackward: true,
        startDate: startDate,
        endDate:end_date
      });

    });

  })(jQuery);
</script>
</body>
</html>
