<footer></footer>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-sm">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Confirmation</h5>
          <button type="button" class="close text-dark cancelDelete" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body pt-0 text-center">
         <p>Are you sure?<br>you want to <span class="enaDis"></span> delete  this record.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-link cancelDelete" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style deleteConfirm" type="button" data-dismiss="modal">Confirm</button>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function changeStatus(className,functionName){
    $(document).on("click",".changeStatus",function() {
      if($('.enaDis').html()=='Active')
        var is_active="1";
      else
        var is_active="0";
      var idarr=$(".enaDis").attr("id").split('_');
      var id=idarr[1];
      $.ajax({
        url: "<?php echo base_url();?>admin/"+className+"/changeStatus",
        type: 'POST',
        data: {'is_active':is_active,'id':id}, 
        success: function(data) {
          if(data) {
            $('#deactiveModal').modal('hide');
          } else { 
          }
        }
      }); 
    });
  }
    $(document).on("click",".deactiveCheck",function() {
      var lablefor = $(this).attr('id');
      var idarr=lablefor.split('_');
      var id=idarr[1];
      $('#tagoff').val(lablefor);
      if ($(this).prop("checked") == true) {
        $(this).parents('tr').removeClass('disable');
        $('#deactiveModal').modal('show');
        $(this).closest("label").find("span").html("Deactivate");
        $('.enaDis').html('Deactivate');
        $('.enaDis').attr('id', 'eanble_'+id);
      }
      else if ($(this).prop("checked") == false) {
        $('#deactiveModal').modal('show');
        $(this).closest("label").find("span").html("Active");
        $('.enaDis').html('Active')

        $('.enaDis').attr('id', 'disable_'+id);
        $(this).parents('tr').addClass('disable');
      }
    });
  $(document).on("click",".cancelToggle",function() {
    $('#deactiveModal').modal('hide');
    var id=$('#tagoff').val();
    $('#'+id).trigger('click');
  });
    $(document).on("click",".delete",function() {
      var id = $(this).attr('id');
      $('#deleteModal').modal('show');
      $('.enaDis').attr('id', 'eanble_'+id);


    });
  
  $(document).on("click",".cancelDelete",function() {
    $('#deleteModal').modal('hide');
  });
function numbersonly(e){
    var unicode=e.charCode? e.charCode : e.keyCode
    if (unicode!=8){ //if the key isn't the backspace key (which we should allow)
        if (unicode<48||unicode>57) //if not a number
            return false //disable key press
    }
}
</script>
</body>
</html>