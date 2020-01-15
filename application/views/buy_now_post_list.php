<div class="pageArea">
  <div class="pageHeader clearfix">
 <!--    <h2 class="float-left pageTitle"><a href="<?php //echo base_url('brands');?>" class="text-white mr-2" onClick="history.back();" value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">View Details</span></h2> -->
    <h2 class="float-left pageTitle"> <span class="ml-2">View Posts</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>

     <div class="pageTabs">
      <ul>
        
        <!-- <li><a href="#" class="">Posts</a></li> -->
        <li><a href="#" class="<?php if($this->uri->segment(1)=='posts') echo "active";?>">Posts</a></li>
      </ul>
    </div>
<div class="pageBody mt-0">
  <div class="pageFilter clearfix">
    <div class="pageFilter-links"><a class="btn btn-style btn-sm text-uppercase" href="<?php echo base_url('create-buy-now-posts');?>">Create Buy Now Post</a></div>
  </div>
  <table id="ajax_table">
    <thead>
      <tr>
        <!-- <th class="text-center">Promo</th> -->
        <th>Post Details</th>        
        <th>Post Type</th>
        <th class="text-center">Publish Date</th>
        <th class="text-center">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
  </table>
  <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val = "0">Load More</a></div> 
</div>
</div>
<script type="text/javascript">
  (function($) {
    'use strict';
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

  })(jQuery);
  $(document).ready(function(){
    getPosts(0);
    Toggle();
    changeStatus('posts','changeStatus');
    $("#search_text").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    $("#load_more").click(function(e){
      e.preventDefault();
      var page = $(this).data('val');
      getPosts(page,'');
    });
  });
  var getPosts = function(page){
    $.ajax({
      url:"<?php echo base_url() ?>getBuyPosts/"+page,
      type:'POST',
      dataType: "json"      
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
</script>
</body>
</html>
