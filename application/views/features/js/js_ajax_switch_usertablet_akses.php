<script type="text/javascript">
jQuery(document).ready(function ($) {
  $('.switch_access').on('change', function(){
    var $this = $(this);
    var el = '#hidden-'+$this.data('tag');
    if($this.prop('checked')){
      $(el).val("on")
    }else{
      $(el).val("off")
    }
  })
});

function switchUserAksesOnOffChanged(inputcheckbox) {
  var $ = jQuery();
  var ischecked = inputcheckbox.checked;
  var kol = inputcheckbox.getAttribute('data-tag');
  var username = '<?=$selecteduser?>';
  var outlet = '<?=$selectedoutlet?>';
  jQuery.post("<?=base_url().'ajax/updateaksesusertablet';?>", {
    "outlet": outlet,
    "username": username,
    "kol": kol,
    "isaktif": ischecked
  }).error(function(xmlhttprequest, textstatus, message) {
    if(nama != null) {
      alert('Gagal mengupdate hak akses ' + nama);
    } else {
      alert('Gagal mengupdate hak akses ' + kol);
    }
  })
}

</script>
