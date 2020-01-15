<div class="pageBody mt-0">
  <table id="ajax_table">
    <thead>
      <tr>
        <th>User Name</th>
        <th class="text-center">Rating</th>
        <th class="text-center">Added On</th>
        <th>Review</th>
        <th class="text-center">Actions</th>
        <th class="text-center"></th>
      </tr>
    </thead>
    <tbody id="tbody">
    <!--   <tr>
        <td><a href="users_detail.html" target="_blank" class="user-link font-weight-bold">Jane Mary</a></td>
        <td class="text-center">4.0</td>
        <td class="text-center">15 Mar 2019</td>
        <td><p class="limitText">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p></td>
        <td class="text-center"><a href="#" class="text-link" data-toggle="modal" data-target="#moreModal">Read More</a></td>
        <td class="text-center"><a href="#" class="text-danger">Remove</a></td>
      </tr> -->
    </tbody>
  </table>  <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val = "0">Load More</a></div> 
</div>
</div>
<footer></footer>

<!-- MORE MODAL -->
<div class="modal fade" id="moreModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content ">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary" id="user_name"></h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="pb-0 pl-1 pr-1" >
            <p id="reviews" style="text-align: center;"></p>
          </div>
        </div>
        <div class="modal-footer">
     <!--      <button class="btn btn-link" type="button" data-dismiss="modal">Remove</button> -->
          <button class="btn btn-style" type="button" data-dismiss="modal">OK</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPT --> 
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>  -->
<script type="text/javascript">
  (function($) {
    'use strict';

    $('.item').each(function() {
     $(this).on('click', function() {
       $(this).addClass('active');
       $(this).closest('.owl-item').siblings().find('.item').removeClass('active');
     });
   });

    $('#gallerySilder').owlCarousel({
      margin: 10,
      nav: true,
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
    getReviews(0);
    deleteConfirm('reviews','delete');

    function deleteConfirm(className,functionName){ 
    $(document).on("click",".deleteConfirm",function() {
      var idarr=$(".enaDis").attr("id").split('_');
      var id=idarr[1];
      $.ajax({
        url: "<?php echo base_url();?>admin/"+className+"/delete",
        type: 'POST',
        data: {'is_active':0,'id':id}, 
        success: function(data) {
          if(data) {
            $('#deleteModal').modal('hide');
            $("#tbody").html('');
            getReviews(0);
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
      getReviews(page,'');
    });
  });
  var getReviews = function(page){
    $.ajax({
      url:"<?php echo base_url() ?>getReviews/"+page,
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
 function readMore(id,name,reviewtext){
         $("#user_name").html(name);
         $("#reviews").html(reviewtext);
         $('#moreModal').modal('show');
  }
  //read more
  /*$(document).on("click",".readmore",function() {
      var id = $(this).attr('id');
      var review_text=$('#review_text_'+id).val();
      alert(review_text);
     
      $('#moreModal').modal('show');


    });*/

    //readMore();
 
</script>
</body>
</html>
