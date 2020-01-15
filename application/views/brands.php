<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle">All <?php echo $title;?></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <div class="pageBody mt-4">
      <div class="pageFilter clearfix">
        <div class="pageFilter-search">
          <input class="form-control" type="text" placeholder="Search" name="search_text" id="search_text"  />
          <button>Go</button>
        </div>
        <div class="pageFilter-links"><a class="btn btn-style btn-sm text-uppercase" href="<?php echo base_url('create-brands');?>">Create New Brand</a></div>
      </div>
      <table id="ajax_table">
        <thead>
          <tr>
            <th>Brand Name</th>
            <th class="text-center">Total Campaigns</th>
            <th class="text-center">Samples</th>
            <th class="text-center">Total Posts</th>
            <th class="text-center">Promo Codes</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
      <tbody id="tbody">
      </tbody>
    </table>
    <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val = "0">Load More</a></div> 
  </div>
</div>
<footer></footer>
<!-- DEACTIVE MESSAGE MODAL -->
<div class="modal fade" id="deactiveModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-sm">
      <input type="hidden" id="tagoff" >
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Confirmation</h5>
          <button type="button" class="close text-dark cancelToggle" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body pt-0 text-center">
         <p>Are you sure?<br>you want to <span class="enaDis"></span>  this brand.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-link cancelToggle" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style changeStatus" type="button" data-dismiss="modal">Confirm</button>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function($) {
    'use strict';
})(jQuery);

$(document).ready(function(){
  getUsers(0);
  //Toggle();
  changeStatus('brands','changeStatus');
  $("#search_text").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  $("#load_more").click(function(e){
    e.preventDefault();
    var page = $(this).data('val');
    getUsers(page,'');
  });
});
var getUsers = function(page){
  $.ajax({
    url:"<?php echo base_url() ?>getBrands/"+page,
    type:'POST',
    dataType: "json",
    data: {'search_text':''}
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
