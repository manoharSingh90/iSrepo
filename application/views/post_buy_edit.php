<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a href="<?php echo base_url('posts');?>" class="text-white mr-2">Back</a> | <span class="ml-2">Create New Post</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>

    <?php 
    $attributes = array('id' => 'posts-form');
    echo form_open('admin/posts/editBuyPosts', $attributes);
    $qr_code_url='';
   // print_r($postDtl);
    $readonly="readonly";
    if($postDtl[0]->is_publish=='0')
      $readonly="";
    /*if(strpos($postDtl[0]->qr_code_url,"assets/post/coupon/") !== false){
    $qr_code_url= explode('assets/post/coupon/', $postDtl[0]->qr_code_url) ['1'];
   }*/
    ?>
    
    <input type="text" class="hidden" value="<?php echo $postDtl[0]->id ;?>" name="post_id" id="post_id" readonly/>
    <input type="text" name="button_type" id="button_type" class="hidden"/>
    <div class="createInfo mt-1">
      <h2>Post Details</h2>
      <div class="pb-5">
        <div class="row align-items-center ">
          <div class="col-12 col-md-4">
            <input type="text" class="form-control" id="buynowUrlField" value="<?php echo $postDtl[0]->buy_now_url ;?>" placeholder="Buy now url" name="buy_now_url" required />
            </div>
            
            <div class="col-12 col-md-5">
              <textarea class="form-control" placeholder="Description" name="post_desc" required><?php echo $postDtl[0]->post_desc ;?></textarea>
            </div>
          </div>
        </div>
        <h2>Media</h2>
        <small>Image size: 1 MB Max | Min Image Resolution: 1125 X 470 | Video Size: 5 MB Max an MP4 Format Only</small>
        <div class="pb-1">
          <ul class="postImages squareImg mt-1">
            <li>
              <?php if($postDtl[0]->banner_type=="1") { ;?><div class="boxImg yesCrop">
              <?php if($postDtl[0]->post_banner_url!="" && file_exists('assets/post/banner/'.$postDtl[0]->post_banner_url)) { ?><img src="<?php  echo base_url('assets/post/banner/'.$postDtl[0]->post_banner_url);?>" alt="<?php //echo $brandDtl->brand_name ;?>"></div><?php } ?>
              <?php } else if($postDtl[0]->banner_type=="2") { ?>
                <div class="boxImg noCrop"><video src="<?php if(file_exists('assets/post/banner/'.$postDtl[0]->post_banner_url)) echo base_url('assets/post/banner/'.$postDtl[0]->post_banner_url);?>"></video>
                </div>
              <?php } ?>
               <input type="file" class="uploadFile" id="uploadDoc-1"data-width="1125" data-height="470" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4" <?php if($postDtl[0]->post_banner_url=="" || !file_exists('assets/post/banner/'.$postDtl[0]->post_banner_url)) echo "required";?>/>

                <input type="text" value="" name="imgupload" id="imgupload-1" class="hidden" />
                <a href="#" class="removeImg" style="display: inline;"><b>X</b> File: <?php  echo $postDtl[0]->post_banner_url;?></a> </li>
                <input type="hidden" name="old_imgupload" value="<?php echo $postDtl[0]->post_banner_url;?>"/>
                <input type="hidden" name="banner_type" value="<?php echo $postDtl[0]->banner_type;?>"/>
            </li>
          </ul>
        </div>
        <hr>
        
        <div class="pt-2 pb-2 text-right">
          <!-- <button class="btn btn-link text-dark mr-3" >Cancel</button> -->
          <button class="btn btn-style mr-2" id="create">Save</button>
          <button class="btn btn-style" id="publish">Publish</button>
        </div>
      </div>
      <?php echo form_close();?>
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
                <div class="img-container"> <!-- <img id="imageCrop" src="assets/img/isamplez_logo.png" alt="Picture"> --> </div>
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

<script type="text/javascript">
  (function($) {
    'use strict';    
    
   // FORM SUBMIT
    $(document).on('click', '#create', function() {
      submitform("create");
   })
    $(document).on('click', '#publish', function() {
     submitform("publish");
   })
  function submitform(type){
    $("#button_type").val(type);
     $("#posts-form").validate({
      errorElement: 'small',
      rules: {
        buy_now_url: {
             url: true,
        },
    },
    message:{
        buy_now_url: {
            url: "Please enter valid url",
        },
    },
      submitHandler: function() {
        $("#"+type).attr("disabled", true).addClass("loading");
        $('#startConfetti').trigger('click');
        var user = $('#posts-form').serialize();
        $.ajax({  
          type: "POST",  
          url:  "<?php echo base_url(); ?>" + "admin/posts/editBuyPosts",  
          data: user,  
          cache: false,  
          success: function(result){  
           // $("#publish,#create").attr("disabled", false).removeClass("loading");
            if(result=="success"){  
                 window.location.replace("<?php echo base_url('posts');?>");  
            }  
           else{  
                 //$("#publish,#create").attr("disabled", false).removeClass("loading"); 
                   $("#"+type).attr("disabled", false).removeClass("loading");
            } 
            
          }   

            });  
      }
    });
   }
 // UPLOAD FILE // UPLOAD POST
 $(document).on('click', '.uploadLink', function() {
 var $fileUpload = $(this).closest('li').find('.uploadFile')
 $fileUpload.trigger('click');
});

$(document).on('click', '.removeImg', function() {
  var $itemWrap = $(this).closest('ul');
  var $itemUpload = $(this).closest('li');
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p> </div> <input type="file" class="uploadFile" id="uploadDoc-1" accept="image/png, image/jpeg, image/jpg, video/mp4" required ><input type="text" class="hidden" value="" name="imgupload" id="imgupload-1" /> <div class="removeImg"><b>X</b> File: </div> </li>'
  $itemWrap.append(uploadData);
  $itemUpload.remove();
});

$(document).on('change', '.uploadFile', function(e) {
  readURL(this);
});


function readURL(input) {
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
  var count = dNum.getFullYear() + '' + (dNum.getMonth() + 1) + '' + dNum.getDate() + '' + dNum.getHours() + '' + dNum.getMinutes() + '' + dNum.getSeconds();
  var counter = count;

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var $fileName = input.files[0].name
    var $fileSize = input.files[0].size
    var $fileMB = Math.round($fileSize / 1024);
    var fileExtension = ['jpeg', 'jpg', 'png', 'mp4'];

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
            $callRadio.show();
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
              $fileinfo.addClass('yesCrop').html('').append('<img src="' + e.target.result + '" alt="#" />')
              $callRadio.show();
              $box.after(uploadData);
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

 // UPLOAD PIC
///vimlesh
$(document).on('click', '.uploadClick', function() {
 var $fileUpload = $(this).closest('li').find('.uploadPic')
 $fileUpload.trigger('click');
});

$(document).on('click', '.removePic', function() {
  var $itemWrap = $(this).closest('ul');
  var $itemUpload = $(this).closest('li');
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadClick">+ Upload QR/BAR Code</p> </div> <input type="file" class="uploadPic" id="uploadPic-1" accept="image/png, image/jpeg, image/jpg"><input type="text" class="hidden" value="" name="couponDoc" id="couponDoc-1" /> <div class="removePic"><b>X</b> File: </div> </li>'
  $itemWrap.append(uploadData);
  $itemUpload.remove();
});

$(document).on('change', '.uploadPic', function(e) {
  picURL(this);
});


function picURL(input) {
  var $this = $(input);
  var $thisID = $this.attr('id');
  var $width = $this.attr('data-width');
  var $height = $this.attr('data-height');
  var $vidSize = $this.attr('data-video-size')

  var $imgSize = $this.attr('data-img-size')
  var $box = $this.closest('li');
  var $nameText = $box.find('.removePic');
  var $callRadio = $box.find('.custom-control');
  var $uploadLink = $box.find('.uploadLink');
  var $fileinfo = $box.find('.boxImg');
  var $imgInput = $fileinfo.find('img');
  var dNum = new Date();
  var count = dNum.getFullYear() + '' + (dNum.getMonth() + 1) + '' + dNum.getDate() + '' + dNum.getHours() + '' + dNum.getMinutes() + '' + dNum.getSeconds();
  var counter = count;

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var $fileName = input.files[0].name
    var $fileSize = input.files[0].size
    var $fileMB = Math.round($fileSize / 1024);
    var fileExtension = ['jpeg', 'jpg', 'png'];

    if ($.inArray($this.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
      $('#exnErrorModal').modal();
      console.log('FILE TYPE ERROR: ' + input.files[0].type)
      return false;
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
            $fileinfo.addClass('yesCrop').html('').append('<img src="' + e.target.result + '" alt="#" />')
            $callRadio.show();
            $box.after(uploadData);
            console.log('IMAGE OK')
          }
        }
        img.src = reader.result;
      }

    };
    reader.readAsDataURL(input.files[0]);
  }
}
///vimlesh

})(jQuery);
</script>
</body>
</html>
