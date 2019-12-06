<?php
/**
* Created by PhpStorm.
* User: Husnan
* Date: 12/12/2015
* Time: 16:02
*/ ?>

<script type="text/javascript">
jQuery(document).ready(function ($) {
  $('#grid-item').on('click','tbody tr .konfirmasihapus',function(){
    var $this = $(this);
    var username = $this.data('tag');

    $('#modalHapus').modal('show');
    $('#modalHapus').find('#delete-username').text(username)
    $('#modalHapus').find('#modal-parameter').val(username)
  });

  $('#btn-delete').on('click', function(){
    var $this = $(this);
    $.ajax({
      method:"POST",
      url: "<?=base_url().'ajax/deleteuser';?>",
      data: {
        username : $('#modalHapus').find('#modal-parameter').val()
      },
      dataType:'JSON',
      beforeSend: function(xhr){
          $this.attr('disabled', true);
          $this.html('<i class="fa fa-refresh fa-spin"></i> Menghapus');
      },
      success: function(response){
          $this.html('Yakin');
          var obj = response;
          if (obj.code == 200) {
            $('#modalHapus').modal('hide');
            $('#alert-deleteuser').show();
            setTimeout(function(){
              window.location.replace("<?=base_url().'perusahaan/usercloud'?>");
            }, 2000);
          }
      }
    })
  })
});
</script>
