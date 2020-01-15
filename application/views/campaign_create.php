<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2"  href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>"  onClick="history.back();"  value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">Create New Campaign</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <div class="pageTabs mt-1 extrawWide">  
      <ul>
        <?php //echo $campaign_id;?>
        <li><a href="<?php echo base_url('create-campaigns/'.$brand_id);?>" class="<?php if($this->uri->segment(1)=='create-campaigns') echo "active";?>" class="disabled" >Campaign</a></li>
        <li><a href="<?php  if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-samples') echo "active";?>">Samples</a></li>
        <li><a href="<?php //if($campaign_id=='') 
        echo  "javascript: void(0)" ;
        //else echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-targetAudience') echo "active";?>">Target Audience</a></li>
        <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-questionnaire') echo "active";?>">Questionnaire</a></li>
        <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-campaign-brand') echo "active";?>">Brand Details</a></li>
      </ul>
    </div>
    <div class="createInfo">
    <?php 
    $attributes = array('id' => 'campaigns-form');
    echo form_open('admin/campaigns/addCamapigns', $attributes);?>
    <div id="err_msg" style="display: none">   
      <h2 style="color: red"><div id="msg"> </div></h2>
    </div> 
    <input type="hidden" name="brand_id" value="<?php echo $brand_id;?>">
      <div class="pt-2 pb-5">
        <h2>Basic Details</h2>
        <div class="row ">
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm">Campaign Title</label>
            <input type="text" name="campaign_name" id="campaign_name" class="form-control" required/>
          </div>
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm">Description</label>
            <textarea class="form-control" name="campaign_desc" id="campaign_desc" required></textarea>
          </div>
          <div class="col-12 col-md-3">
            <div>
              <label class="col-form-label-sm">Start Date</label>
              <input id="startDateInput" type="text" name="start_date" id="start_date"  class="form-control dateIcon" required autocomplete="off" onkeydown="return false;"/>
            </div>
            <div class="pt-3">
              <label class="col-form-label-sm ">End Date</label>
              <input id="endDateInput" type="text" name="end_date" id="end_date"  class="form-control dateIcon" required autocomplete="off" onkeydown="return false;"/>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm">Buy Now Url</label>
            <input type="text" name="buy_now_link" id="buy_now_link" class="form-control"/>
          </div>
        </div>
      </div>
      <h2>Media</h2>
      <small>Image size: 1 MB Max | Min Image Resolution: 1125 X 470 | Video Size: 5 MB Max an MP4 Format Only</small>
      <div class="pb-1">
        <ul class="postImages postImagem mt-1">
          <li>
            <div class="boxImg" rel="1"><img src="" alt="#" style="display:none;">
              <p class="text-center text-uppercase font-weight-bold text-link uploadLink" rel="1">+ Upload file</p>
            </div>
            <input type="file" class="uploadFile uploadFilem" rel="1" id="uploadDoc-1" data-width="1125" data-height="470" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4" required/>
            <input type="text" value="" name="imgupload[]" id="imgupload-1" class="hidden" />
            <div class="removeImg"  rel="1"><b>X</b> File: Camp1.jpg</div>
            <div class="custom-control custom-radio hidden">
              <input type="radio" class="custom-control-input defaultCheck" name="makedefault" id="defaultCheck" value="0">
              <label class="custom-control-label font-weight-bold" for="defaultCheck">Make Cover Image</label>
            </div>
          </li>
        </ul>
      </div>
	  <h2>Available for purchase</h2>
      <small>Image size: 1 MB Max | Min Image Resolution: 200 X 200</small>
       <div class="pb-1">
          <ul class="postImages postImagesl squareImg mt-1">
			
              <li>
                <div class="boxImg locationBox"><img src="" alt="#" style="display: none;" >
                  <p class="text-center text-uppercase font-weight-bold text-link uploadLink" rel="2">+ Upload file</p>
                </div>
				<input type="file" class="uploadFile uploadFilel" rel="2"  id="uploadDocl-1>" data-width="200" data-height="200" data-img-size="5120"  accept="image/png, image/jpeg, image/jpg, video/mp4" />
				<input type="hidden" value="" name="imguploadl[]" id="imguploadl-<?php echo ($countlo+1); ?>" class="hidden imguplod" />
               <div class="removeImg" rel="2"><b>X</b> File: Camp1.jpg</div>
               
              </li>
			 
            </ul>
          </div>
      <div class="pt-2 pb-2 text-right">
        <button class="btn btn-style" id="next">Next</button>
      </div>
    <?php echo form_close();?>
    </div>
  </div>
  <footer></footer>

  <!-- CROP MODAL -->
  <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">CROP</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body pt-0">
          <div class="row">
            <div class="col-12">
              <div class="img-container"> <img id="imageCrop" src="" alt="Picture"> </div>
            </div>
            <div class="col-12 docs-buttons">
              <div class="btn-group hidden">
                <button type="button" class="btn btn-primary" data-method="crop" title="Crop">Crop</button>
                <button type="button" class="btn btn-primary" data-method="clear" title="Clear">Clear</button>
                <button type="button" class="btn btn-primary" data-method="disable" title="Disable">Disable</button>
                <button type="button" class="btn btn-primary" data-method="enable" title="Enable">Enable</button>
                <button type="button" class="btn btn-primary" data-method="reset" title="Reset">Reset</button>
                <button id="destroyCrop" type="button" class="btn btn-primary" data-method="destroy" title="Destroy">Destroy</button>
                <button id="getCrop" type="button" class="btn btn-success" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 1125, &quot;maxHeight&quot;: 470 }"> Get Cropped Canvas </button>
                <div id="afterCrop"></div>

              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-link" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style" type="button" data-dismiss="modal" id="cropBtn">Crop</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ERROR MESSAGE MODAL -->
  <div class="modal fade" id="reqErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Error</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body text-center">
          <p>Please upload required size image or video</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-style" type="button" data-dismiss="modal">Done</button>
        </div>
      </div>
    </div>
  </div>

  <!-- EXN. ERROR MESSAGE MODAL -->
  <div class="modal fade" id="exnErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Error</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body text-center">
          <p>Invalid Extension!</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-style" type="button" data-dismiss="modal">Done</button>
        </div>
      </div>
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
          <p>Please select one media as a cover image.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-style" type="button" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    (function($) {
      'use strict';
      // FORM SUBMIT
      $("#campaigns-form").validate({
        errorElement: 'small',
        focusInvalid: false,
        rules: {
          buy_now_link: {
               url: true,
          },
      },
      message:{
          buy_now_link: {
              url: "Please enter valid url",
          },
      },
        submitHandler: function() {
          if (!$("input[name='makedefault']:checked").val()) {
             $('#ErrorModal').modal();
             return false;
          }
          $("#next").attr("disabled", true);
          $('#startConfetti').trigger('click');
          var user = $('#campaigns-form').serialize();
          $.ajax({  
            type: "POST",  
            url:  "<?php echo base_url(); ?>" + "admin/campaigns/addCamapigns",  
            data: user,  
            cache: false,  
            success: function(campaign_id){  
             if(campaign_id>0){  
              window.location.replace("<?php echo base_url('create-samples/'.$brand_id.'/');?>"+campaign_id);  
            }  
            /* else  {
                jQuery("div#err_msg").show();  
                jQuery("div#msg").html("error"); 
              }*/


              $("#next").attr("disabled", false);
            }  

            });  
        }
      });
	var cropingRationType=1;
    // DATE PICKER
    var dateTime = new Date();
    dateTime = moment(dateTime).format('DD-MM-YYYY');
    $('#endDateInput').attr('data-date', dateTime);

    $('#startDateInput').dateRangePicker({
      format: 'DD-MM-YYYY',
      autoClose: true,
      singleDate: true,
      showTopbar: false,
      singleMonth: true,
      selectForward: true,
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
        startDate: startDate
      });
    });


 // UPLOAD FILE
 $(document).on('click', '.uploadLink', function() {
	if($(this).attr('rel')==1)
     var $fileUpload = $(this).closest('li').find('.uploadFilem')
	 else
	var $fileUpload = $(this).closest('li').find('.uploadFilel')
	$fileUpload.trigger('click');
 });

  $(document).on('click', '.removeImg', function() {
	var totl=$('.locaton_name').length;
	var type=$(this).attr('rel');
	var $itemUpload = $(this).closest('li');
	$itemUpload.remove();
	if(type==2 && totl==5)
	{
		var uploadData = '<li> <div class="boxImg locationBox"><p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p> </div> <input type="file" rel="2" class="uploadFile uploadFilel" id="uploadDocl-'+totl+'" data-width="200" data-height="200" data-img-size="5120"  accept="image/png, image/jpeg, image/jpg"><input type="text" class="hidden imguplod" value="" name="imguploadl[]"  id="imguploadl-'+totl+'" /><input type="text" name="camp_loation_id[]" value="" class="hidden"><div class="removeImg" rel="2"><b>X</b> File: </div> </li>';
		$('.postImagesl').append(uploadData);
	}
});
 
 function readURL(input,counter,type) {
	if(type==1)
	cropingRationType=1;
	else
	cropingRationType=2;

  var counterr=$('.postImagesm li').length;
  if(cropingRationType==2)
  counterr=$('.postImagesl li').length;
   var $this = $(input);
   var $thisID = $this.attr('id');
   var $width = $this.attr('data-width');
   var $height = $this.attr('data-height');
   var $vidSize = $this.attr('data-video-size')

   var $imgSize = $this.attr('data-img-size')
   var $box = $this.closest('li');
   var $nameText = $box.find('.removeImg');
   var $callRadio = $box.find('.custom-control');
   var $uploadLink = $box.find('.uploadLink');
   var $fileinfo = $box.find('.boxImg');
   var $imgInput = $fileinfo.find('img');
   var dNum = new Date();
 // var count = dNum.getFullYear() + '' + (dNum.getMonth() + 1) + '' + dNum.getDate() + '' + dNum.getHours() + '' + dNum.getMinutes() + '' + dNum.getSeconds();
 // var counter = count;
 if (input.files && input.files[0]) {
  var reader = new FileReader();
  var $fileName = input.files[0].name;
  var $fileSize = input.files[0].size;
  var $fileMB = Math.round($fileSize / 1024);
  if(cropingRationType==1)
  {
	  var fileExtension = ['jpeg', 'jpg', 'png', 'mp4'];
	  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadLink" rel="1">+ Upload file</p> </div> <input type="file" rel="1" class="uploadFile uploadFilem" id="uploadDoc-'+counter+'" data-width="1125" data-height="470" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4"><input type="text" class="hidden" value="" name="imgupload[]"  id="imgupload-'+counter+'" /> <input type="text" name="banner_url[]" value="" class="hidden"><input type="text" name="camp_banner_id[]" value="" class="hidden"> <input type="text" name="banner_type[]" value="" class="hidden"><div class="removeImg" rel="1"><b>X</b> File: </div> <div class="custom-control custom-radio hidden"> <input type="radio" class="custom-control-input defaultCheck" id="defaultCheck-0'+counter+'" name="makedefault" value="'+counterr+'"> <label class="custom-control-label font-weight-bold" for="defaultCheck-0'+counter+'">Make Cover Image</label> </div> </li>'
  }
  else
  {
	    var totl=$('.locationBox').length+1;
	    var fileExtension = ['jpeg', 'jpg', 'png'];
		var uploadData = '<li> <div class="boxImg locationBox"><p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p> </div> <input type="file" rel="2" class="uploadFile uploadFilel" id="uploadDocl-'+totl+'" data-width="200" data-height="200" data-img-size="5120"  accept="image/png, image/jpeg, image/jpg"><input type="text" class="hidden imguplod" value="" name="imguploadl[]"  id="imguploadl-'+totl+'" /><input type="text" name="camp_loation_id[]" value="" class="hidden"><div class="removeImg" rel="2"><b>X</b> File: </div> </li>';
		if(totl==6)
		uploadData="";
  }
  if ($.inArray($this.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
    $('#exnErrorModal').modal();
    console.log('FILE TYPE ERROR: ' + input.files[0].type)
    return false;
  } else {
    
    if (input.files[0].type === 'video/mp4') {
      reader.onload = function(e) {
        if ($fileMB > $vidSize) {
          $('#reqErrorModal').modal();
          console.log('FILE AND SIZE ERROR');
        } else {
          $nameText.html('<b>X</b> File: ' + $fileName).show();
          $this.next('input').val(e.target.result)
           // $("#imgupload-" +(counter-1)).val($fileName);
           $fileinfo.addClass('noCrop').html('').append('<video src="' + e.target.result + '" ></video>')
          // $callRadio.show();
           $box.after(uploadData);
           console.log('VIDEO OK')
         }
       }
     } else {

      reader.onload = function(e) {
        var img = new Image;
        img.onload = function() {
          console.log('------- FILE ----------')
          console.log('SIZE: ' + $fileMB)
          console.log('Width: ' + img.width)
          console.log('Height: ' + img.width)
          console.log('Type: ' + input.files[0].type)
          console.log('-----------------');

          if (img.width < $width || img.height < $height || $fileMB > $imgSize) {
            $('#reqErrorModal').modal();
            console.log('FILE AND SIZE ERROR');
          } else {
            $nameText.html('<b>X</b> File: ' + $fileName).show();
            $this.next('input').val(e.target.result)
			if(cropingRationType==2)
			$nameText.before('<input type="text" placeholder="Title" name="lname[]" class="form-control locaton_name lname mt-2"/><input type="text" placeholder="Logo Url" name="url[]" class="form-control url url mt-2"/>');
            $fileinfo.addClass('yesCrop').html('').append('<img src="' + e.target.result + '" alt="#" />')
            $callRadio.show();
            $box.after(uploadData);
			$box.find('.yesCrop').attr('rel',cropingRationType);
            $box.find('.yesCrop').trigger('click');
            console.log('IMAGE OK')
          }
        }
        img.src = reader.result;
      }


    }

  };
  reader.readAsDataURL(input.files[0]);
}
}

$(document).on('change', '.uploadFilem', function(e) {
  var type= $(this).attr('rel');
  var idArr= this.id.split("-");
  var counter=(Number(idArr[1])+1);
   readURL(this,counter,type);
});
$(document).on('change', '.uploadFilel', function(e) {
  var type= $(this).attr('rel');
  var idArr= this.id.split("-");
  var counter=(Number(idArr[1])+1);
  readURL(this,counter,type);
});

$(document).on('click', '.yesCrop', function(e) {
	var type= $(this).attr('rel');
	if(type==1)
	cropingRationType=1;
	else 
	cropingRationType=2;
  var $imageID = $(this).next('input').attr('id');
  var $image = $(this).find('img').attr('src');
  $('#imageCrop').attr('src',$image);
  $('#afterCrop').attr('data-rel',$imageID);
  $('#cropModal').modal();
});

$('#cropModal').on('show.bs.modal', function () {
  $('#destroyCrop').trigger('click');
  $('#afterCrop').html('');
  var aspectRatio=1125/470;
  if(cropingRationType==2)
	aspectRatio=200/200;
  setTimeout( function(){
    var $image = $('#imageCrop');
    $image.cropper({
      aspectRatio: aspectRatio,
      zoomable: true,
      rotatable: false,
      background: false,
      zoomOnTouch: false,
      zoomOnWheel: true,
      restore: true,
      strict: false,
      responsive: true,
      dragMode:'move',
      dragCrop: true,
      checkCrossOrigin:false,
      cropBoxMovable: true,
      cropBoxResizable: false,
                      //    minCropBoxWidth:1125,
                      minCropBoxHeight:280
                    });   
  }, 400);
});


$(document).on('click', '#cropBtn', function(e) {
  $('#getCrop').trigger('click');
  var $imgID = $('#afterCrop').attr('data-rel');
  var $imgData = $('#afterCrop').html();
  $('#'+$imgID ).next('input').addClass('ddddddd').val($imgData);
  $('#'+$imgID).prev('div').find('img').attr('src', $imgData);
});
/*$(document).on('click', '.defaultCheck', function(e) {
  $(".defaultCheck").val(2);
var id=$(this).attr('id');
 if($("#"+id).attr("checked")){
  alert('ff');
  $("#"+id).val("1") ;
 } 
  else{

  $("#"+id).val("2") ;
  alert('NO');
  }
});*/
})(jQuery);
</script>
</body>
</html>

