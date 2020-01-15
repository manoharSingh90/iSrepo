<div class="pageBody mt-0">
  <div class="pageFilter clearfix">
    <div class="pageFilter-links"><a class="btn btn-style text-uppercase btn-sm" href="<?php echo base_url('create-vending/'.$brand_id.'/'.$campaign_id);?>">Create Vending Machine</a></div>
  </div>
  <table id="ajax_table">
    <thead>
      <tr>
        <th>Location Name</th>
        <th>Address</th>
        <th class="text-center">No. of Samples</th>
        <th class="text-center">Actions</th>
        <th class="text-center"></th>
      </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
  </table>
  <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val = "0">Load More</a></div> 
</div>
</div>
<footer></footer>
<!-- MESSAGE MODAL -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-sm">
        <?php 
          $attributes = array('class' => 'text-center', 'id' => 'addSampleform');
          echo form_open('admin/samples/addSamples', $attributes);?>
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Add Sample</h5>
          <button type="button" class="close text-dark cancelSample" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body pt-0 text-center">
          <label class="col-form-label-sm d-block">No. of Samples</label>
          <input type="number" class="form-control text-center w-75 d-inline-block" name="total_samples" id="total_samples" value="" required/>
          <input type="hidden" name="id" id="sample_id" value=""/>
          <input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>"/>
        </div>
        <div class="modal-footer">
          <button class="btn btn-link cancelSample" type="button" data-dismiss="modal">Cancel</button>
        <!--   <button class="btn btn-style addSample" type="button" data-dismiss="modal">Add</button> -->
         <button class="btn btn-style" type="submit">Add</button>
        </div>
      <?php echo form_close();?>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function($) {
    'use strict';
    $("#addSampleform").validate({
      errorElement: 'small',
      submitHandler: function() {
        $('#startConfetti').trigger('click');
        var formData = $('#addSampleform').serialize();
        console.log(formData);
        $.ajax({  
              type: "POST",  
              url:  "<?php echo base_url();?>admin/samples/addSamples",  
              data: formData,  
             // cache: false,  
              success: function(data) {
                if(data) {
                  $('#addModal').modal('hide');
                  $("#tbody").html('');
                  getSamples(0);
                  //$("#stock_id_"+id).val(data);
                } 
              } 
        });  
      }
    });
    $('.item').each(function() {
     $(this).on('click', function() {
       $(this).addClass('active');
       $(this).closest('.owl-item').siblings().find('.item').removeClass('active');
     });
   });
    $('#gallerySilder').owlCarousel({
      margin: 10,
      nav: false,
      autoWidth: false,
      dots: false,
      responsive: {
        0: {
          items: 1,
          nav: true,
        },
        768: {
          items: 6,
        }

      }
    });
    $('.img-link').mediaBox({
      closeImage: 'media/close.png',
      openSpeed: 1000,
      closeSpeed: 800
    });
  })(jQuery);
  $(document).ready(function(){
    getSamples(0);
    deleteConfirm('samples','delete');
    function deleteConfirm(className,functionName){ 
      $(document).on("click",".deleteConfirm",function() {
        var idarr=$(".enaDis").attr("id").split('_');
        var id=idarr[1];
        var campaign_id="<?php echo $campaign_id;?>";
        $.ajax({
          url: "<?php echo base_url();?>admin/"+className+"/delete/"+campaign_id,
          type: 'POST',
          data: {'is_active':0,'id':id}, 
          success: function(data) {
            if(data) {
              $('#deleteModal').modal('hide');
              $("#tbody").html('');
              getSamples(0);
            } else { 
            }
          }
        }); 
      });
    }
    $("#search_text").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    $("#load_more").click(function(e){
      e.preventDefault();
      var page = $(this).data('val');
      getSamples(page);
    });
  });
  var getSamples = function(page){
    $.ajax({
      url:"<?php echo base_url() ?>getSamples/"+page,
      type:'POST',
      dataType: "json",
      data: {'campaign_id':"<?php echo $campDtl[0]->id;?>"}
    }).done(function(response){
      if(response.totalRecords < 10 || response.results=='')
        $("#divLoadMore").hide();
      $("#ajax_table").append(response.results);
      $('#load_more').data('val', ($('#load_more').data('val')+1));
      scroll();
    });
  };
  var scroll  = function(){
    $('html, body').animate({
      scrollTop: $('#load_more').offset().top
    }, 1000);
  };
  function addSample(id){
    $("#sample_id").val(id);
    $('#addModal').modal('show');
  }

  /*$(document).on("click",".addSample",function() {
    var id= $("#sample_id").val();
    var total_samples= $("#total_samples").val();
    if(total_samples=='')

   $.ajax({
      url: "<?php echo base_url();?>admin/samples/addSamples",
      type: 'POST',
      data: {'total_samples':total_samples,'id':id}, 
      success: function(data) {
        if(data) {
          $('#addModal').modal('hide');
          $("#tbody").html('');
          getSamples(0);
          //$("#stock_id_"+id).val(data);
        } else { 
        }
      }
    }); 
  });*/
   
</script>
</body>
</html>
