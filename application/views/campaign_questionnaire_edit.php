<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2"  href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>" onClick="history.back();"  value="Back" style="cursor: pointer">Back</a>  | <span class="ml-2">Edit Campaign</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a>
    </div>
  </div>
  <div class="pageTabs mt-1 extrawWide">
    <ul>
      <li><a  id="camp" href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-campaigns/'.$brand_id.'/'.$campaign_id);?>" class="<?php if($this->uri->segment(1)=='create-campaigns') echo "active";?>" >Campaign</a></li>
      <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-samples') echo "active";?>">Samples</a></li>
      <li><a href="<?php if($campaign_id=='') 
      echo  "javascript: void(0)" ;
      else echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-targetAudience') echo "active";?>">Target Audience</a></li>
      <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='edit-questionnaire') echo "active";?>">Questionnaire</a></li>
      <li class="disabled" ><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-campaign-brand') echo "active";?>" >Brand Details</a></li>
    </ul>
  </div>
  <div class="createInfo">
    <?php 
    $attributes = array('id' => 'questionnaire-form');
    echo form_open('admin/questionnaire/editQuestionnaire', $attributes);?>
    <input type="hidden" name="brand_id" value="<?php echo $brand_id;?>">
    <input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>">
    <input type="hidden" name="review_id" value="<?php if(!empty($reviewQuest)) echo $reviewQuest[0]->review_id;?>">
    <input type="hidden" name="counter" value="<?php echo count($reviewQuest);?>" id="counter">
    <div class="pt-2 pb-5">
      <h2>Feedback Questionnaire</h2>
      <div class="pt-2 pb-5">
       <div id="err_msg" style="display: none">   
         <center><h2 style="color: red"><div id="msg"> </div></h2></center>
       </div> 
       <?php if($reviewQuest) { 
        $counter=1;
        foreach ($reviewQuest as $key => $value) {  ?>
          <div class="questionInput">
            <div class="row"> <div class="col-12 "><hr></div> </div> 
            <div class="row"> 
              <div class="col-12 col-md-5"> 
                <label class="col-form-label-sm">Question</label>
                <input type="text" name="ques_text[<?php echo $counter;?>]" value="<?php echo $value->ques_text;?>" class="form-control" required/>
                <input type="hidden" name="questionnaire_id[<?php echo $counter;?>]" value="<?php echo $value->id;?>">
              </div>
              <div class="col-12 col-md-3">
                <label class="col-form-label-sm">Question Type</label>
                <select class="form-control" name="question_type[<?php echo $counter;?>]" value="<?php echo $value->ques_text;?>" required>
                  <option value="1" <?php if( $value->ques_type=='1') echo"selected";?>>Multiple Choice</option>
                  <option value="2" <?php if( $value->ques_type=='2') echo"selected";?>>Yes/No</option>
                </select>
                <div class="pt-4">
                  <label class="col-form-label-sm ">Enter the Choices:</label>
                  <?php if($reviewAns[$key]) {
                    $innercounter=1; 
                    foreach ($reviewAns[$key] as $values) {  ?>
                      <div class="optionInput optionInp_<?php echo $counter;?>  mt-3">
                        <input type="text" name="answer_text<?php echo $counter;?>[<?php echo $innercounter;?>]" value="<?php echo $values->answer_text;?>" id="option_<?php echo $counter;?><?php echo $innercounter;?>" class="form-control repeatOption_<?php echo $counter;?>" placeholder="Option <?php echo $innercounter;?>" required/>
                        <a href="#" class="removeImg"><b>X</b></a> 
                      </div>
                      <input type="hidden" name="ansId<?php echo $counter;?>[]" value="<?php echo $values->id;?>">
                      <!-- <div class="optionInput optionInp_1  mt-3">
                        <input type="text" name="answer_text1[]" id="option_12" class="form-control repeatOption_1" placeholder="Option 2" required/>
                        <a href="#" class="removeImg"><b>X</b></a> 
                      </div>
                      <div class="optionInput optionInp_1 mt-3">
                        <input type="text" name="answer_text1[]" id="option_13" class="form-control repeatOption_1" placeholder="Option 3" required/>
                        <a href="#" class="removeImg"><b>X</b></a> 
                      </div> -->

                      <?php  $innercounter++ ;} } ?>
                      <div class="appendOptionlist_<?php echo $counter;?>" ></div> 
                    </div>

                    <a href="#" class="text-link font-weight-bold mt-4 d-inline-block adOptionClick_<?php echo $counter;?>">+ Add Another Option</a> 
                  </div>
                  <a href="#" class="removeImg1"><b>X</b></a>
                </div>
              </div>
              <?php $counter++;} } ?>
              <div class="appendQuestionlist" ></div> 
              <div class="row" id="beforeThat">
                <div class="col-12"><hr></div>
              </div>
              <div class="row">
                <div class="col-12"> <a href="#" class="text-link font-weight-bold mt-4 d-inline-block adQuestClick">+ Add Another Question</a></div>
              </div>
            </div>
          </div>
          <div class="pt-2 pb-2 text-right"> <a class="btn btn-link mr-2" href="<?php echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>">Back</a>
            <button class="btn btn-style" id="next">Next</button>
          </div>
          <?php echo form_close();?>
        </div>
      </div>
      <script type="text/javascript">
        (function($) {
    // FORM SUBMIT
    $("#questionnaire-form").validate({
      errorElement: 'small',
      debug: true,
      submitHandler: function() {
        $("input").focus(); 
        $("#next").attr("disabled", true);
        $('#startConfetti').trigger('click');
        var user = $('#questionnaire-form').serialize();
        $.ajax({  
          type: "POST",  
          url:  "<?php echo base_url(); ?>" + "admin/questionnaire/editQuestionnaire",  
          data: user,  
          cache: false,  
          success: function(campaign_id){  
           if(campaign_id>0){  
            console.log(campaign_id);
            /* window.location.replace("<?php //echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>");*/ 
            window.location.replace("<?php echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>");  
          }  
          else  {
            $("#next").attr("disabled", false);
            jQuery("div#err_msg").show();  
            jQuery("div#msg").html('Something went wrong.'); 
          }
        }  
      });  
      }
    });
    'use strict';
    <?php $cnntt=1;
    foreach($reviewQuest as $a){ ?>
      addOptionMore(<?php echo $cnntt;?>);
      <?php $cnntt++; } ?>
      var wrapper2         = $(".appendQuestionlist"); 
      $(document).on("click",".adQuestClick",function(e) {
        e.preventDefault();

        var questTextcnter=parseInt($("#counter").val());
        var newquestTextcnter= (questTextcnter+1);
        $("#counter").val(newquestTextcnter);
      //alert( $("#counter").val());
       //var count= newquestTextcnter;
       var count = $('.questionInput').length+1;
       var newQuestion = '<div  class="questionInput"><div class="row"> <div class="col-12 "><hr></div> </div> <div class="row"> <div class="col-12 col-md-5"> <label class="col-form-label-sm">Question</label> <input type="text" name="ques_text['+count+']" class="form-control" required/> <input type="hidden" name="questionnaire_id['+count+']" value=""></div> <div class="col-12 col-md-3"> <label class="col-form-label-sm">Question Type</label> <select class="form-control" name="question_type['+count+']" required> <option value="1">Multiple Choice</option> <option value="2">Yes/No</option> </select> <div class="pt-4"> <label class="col-form-label-sm ">Enter the Choices:</label> <div class="optionInput optionInp_'+count+'"> <input type="text" name="answer_text'+count+'[1]" id="option_'+count+'1" class="form-control repeatOption_'+count+'" placeholder="Option 1" required/> <a href="#" class="removeImg"><b>X</b></a><input type="hidden" name="ansId'+count+'[]" value=""></div> <div class="optionInput optionInp_'+count+' mt-3"> <input type="text" name="answer_text'+count+'[2]" id="option_'+count+'2" class="form-control repeatOption_'+count+'" placeholder="Option 2" required/> <a href="#" class="removeImg"><b>X</b></a> </div> <div class="optionInput optionInp_'+count+' mt-3"> <input type="text" name="answer_text'+count+'[3]" id="option_'+count+'3" class="form-control repeatOption_'+count+'" placeholder="Option 3" required/> <a href="#" class="removeImg"><b>X</b></a> </div> <div class="appendOptionlist_'+count+'" ></div> </div> <a href="#" class="text-link font-weight-bold mt-4 d-inline-block adOptionClick_'+count+'">+ Add Another Option</a> </div> <a href="#" class="removeImg1"><b>X</b></a></div></div>';
       $(wrapper2).append(newQuestion);
       addOptionMore(count);

     });
    })(jQuery);
    $(document).on("click",".removeImg",function() {
     var optionsId=$(this).closest(".optionInput").find('input').attr('id'); 
     if(optionsId!=undefined){
       $("#"+optionsId).prop('required',false);
     }
     $(this).parents('.optionInput').remove();
   });
    $(document).on("click",".removeImg1",function() {
     var questionId=$(this).closest(".questionInput").find('input').attr('id'); 
     if(questionId!=undefined){
      $("#"+questionId).prop('required',false);
    }
    if($('.questionInput:visible').length>1){
      $(this).closest(".questionInput").find('input').val(''); 
      $(this).closest('.questionInput').hide();
    }
  });
    function addOptionMore(count){
     var wrapper         = $(".appendOptionlist_"+count); 
     $(".adOptionClick_"+count).click(function(e){
      e.preventDefault();
      var optionsId=$(this).closest(".optionInput").find('input').attr('class'); 
      var countt = $('.optionInp_'+count).length+1;
      var html = ' <div class="optionInput optionInp_'+count+' mt-3"><input type="text" name="answer_text'+count+'['+countt+']" id="option_'+count+countt+'" class="form-control repeatOption_'+count+'" placeholder="Option '+countt+'" required/><a href="#" class="removeImg"><b>X</b></a> </div>';
      $(wrapper).append(html);
    });
   }
 </script>
</body>
</html>
