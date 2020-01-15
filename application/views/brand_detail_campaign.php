<div class="pageBody mt-0">
  <div class="pageFilter clearfix">
    <div class="pageFilter-links"><a class="btn btn-style btn-sm text-uppercase" href="<?php echo base_url('create-campaigns/'.$brandDtl[0]->id);?>">Create New Campaign</a></div>
  </div>
  <table id="ajax_table">
    <thead>
      <tr>
        <th></th>
        <th>Campaign Name</th>
        <th class="text-center">Start Date</th>
        <th class="text-center">End Date</th>
        <th class="text-center">Samples<br>
        Redeemed</th>
        <th class="text-center">Promotions<br>
        Redeemed</th>
        <th class="text-center">Total Posts</th>
        <th class="text-center">NPS</th>
        <th class="text-center">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
  </table>
  <div class="pt-4 pb-0 text-center" id="divLoadMore"><a href="#" class="font-weight-bold text-link small" id="load_more" data-val = "0">Load More</a></div> 
</div>
</div>
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
    getCampaigns(0);
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
      getCampaigns(page);
    });

    $(document).on('click','.genRepo',function(){
      var campaign_id = $(this).attr('camp_id');
      $.ajax({
            url: "<?php echo base_url('campaign-completion/');?>"+campaign_id,
            type: "POST",
            async: false,
            
            dataType: "json",
            success: function(rs) {
                console.log(rs);
                if (rs.msg == 'success') {
                    location.href = "<?= base_url('assets/files/');?>"+rs.file;
                    
                }

            }
        });

    })
  });
  var getCampaigns = function(page){
    $.ajax({
      url:"<?php echo base_url() ?>getCampaigns/"+page,
      type:'POST',
      dataType: "json",
      data: {'brand_id':"<?php echo $brandDtl[0]->id;?>"}
    }).done(function(response){
      console.log(response);
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
