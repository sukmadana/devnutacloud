<script type="text/javascript">
  jQuery(document).ready(function($) {

    // Update Password
    var next_state_input = 'text';
    $('.show-old-password').on('click', function() {
      $('input[name="old_password"]').prop('type', next_state_input);
      if (next_state_input == 'text') {
        next_state_input = 'password';
        $('.show-old-password').html('<i class="fa fa-eye-slash"></i>');
      } else {
        next_state_input = 'text';
        $('.show-old-password').html('<i class="fa fa-eye"></i>');
      }
    });

    $('.show-password').on('click', function() {
      $('input[name="password"]').prop('type', next_state_input);
      if (next_state_input == 'text') {
        next_state_input = 'password';
        $('.show-password').html('<i class="fa fa-eye-slash"></i>');
      } else {
        next_state_input = 'text';
        $('.show-password').html('<i class="fa fa-eye"></i>');
      }
    });

    $('.show-konfirmasi-password').on('click', function() {
      $('input[name="konfirmasi_password"]').prop('type', next_state_input);
      if (next_state_input == 'text') {
        next_state_input = 'password';
        $('.show-konfirmasi-password').html('<i class="fa fa-eye-slash"></i>');
      } else {
        next_state_input = 'text';
        $('.show-konfirmasi-password').html('<i class="fa fa-eye"></i>');
      }
    });

    $('#btn-update-password').on('click', function() {
      var $this = $(this);
      var password = $('#formChangePassword').find('input[name="password"]').val();
      $.ajax({
        method: "POST",
        url: "<?= base_url() . 'ajax/updatepasswordusertablet'; ?>",
        data: {
          outlet: "<?= $selectedoutlet ?>",
          username: "<?= $selecteduser ?>",
          old_password: $('#formChangePassword').find('input[name="old_password"]').val(),
          password: $('#formChangePassword').find('input[name="password"]').val(),
          konfirmasi_password: $('#formChangePassword').find('input[name="konfirmasi_password"]').val(),
        },
        dataType: 'JSON',
        beforeSend: function(xhr) {
          $this.attr('disabled', true);
          $this.html('<i class="fa fa-refresh fa-spin"></i> Menyimpan');
        },
        success: function(response) {
          $this.attr('disabled', false);
          $this.html('Simpan');
          if (response.status == 200) {
            $('#modalChangePassword').find('.alert-fixed-danger').hide();
            $('#modalChangePassword').find('.alert-success').text(response.message);
            $('#modalChangePassword').find('.alert-fixed-success').show();

            $('#formChangePassword').find('input[name="old_password"]').val(password);
            $('#formChangePassword').find('input[name="password"]').val('');
            $('#formChangePassword').find('input[name="konfirmasi_password"]').val('');

            setTimeout(function() {
              $('#modalChangePassword').find('.alert-fixed-success').hide();
              $('#modalChangePassword').modal('hide');

              window.location = '<?= base_url() ?>perusahaan/usertabletdetail?user=<?= $selecteduser ?>&outlet=<?= $selectedoutlet ?>';
            }, 2000);
          } else {
            $('#modalChangePassword').find('.alert-fixed-success').hide();
            $('#modalChangePassword').find('.alert-danger').text(response.message);
            $('#modalChangePassword').find('.alert-fixed-danger').show();
          }
        }
      })
    });
  });

  function getDataOutlet() {
    var outlet = document.getElementById('outlet');
    return outlet.options[outlet.selectedIndex].value;
  }

  function selectinge() {
    var selected = getDataOutlet();
    if (!selected)
      return location.href = base_url + "perusahaan/usertablet";

    return location.href = base_url + "perusahaan/usertablet?outlet=" + selected;
  }
</script>