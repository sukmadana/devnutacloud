
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script type="text/javascript">
    var $ = jQuery.noConflict();

    const current_form = $('#form-journal');
    const form_outlet = $('#form-outlet');

    const rowCount = '<?= count($journal['journaldetail']); ?>';

    $.fn.datepicker.dates['ID'] = {
        days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
        daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
        daysMin: ["Mi", "Sn", "Sl", "Ra", "Ka", "Ju", "Sa"],
        months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        today: "Hari ini",
        clear: "Hapus",
        format: "mm/dd/yyyy",
        titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
        weekStart: 0
    };

    $('#journaldate').datepicker({
        orientation: "bottom",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        todayBtn: 'linked',
        language: 'ID',
        format: 'd MM yyyy'
    }).on('changeDate', function (e) {
        var date = e.date;
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();

        $('input[name="journal_date"]').val(year + '-' + month + '-' + day);
        
    });

    $('#journaltime').timepicker({
        timeFormat: 'HH:mm',
        interval: 1,
        minTime: '00',
        maxTime: '23:59',
        defaultTime: "00:00",
        startTime: '00:00',
        dynamic: false,
        dropdown: false,
        scrollbar: false
    });

    $(document).ready(function() {
        current_form.validate({
            validClass: "",
            rules : {
                journaldate: 'required',
                journaltime: 'required',
                note: 'required',
                transactionname: 'required',
                transactioncode: 'required'
            },
            messages : {
                journaldate : 'Tanggal jurnal belum diisi.',
                journaltime : 'Jam jurnal belum diisi.',
                note : 'Keterangan belum diisi.',
                transactionname: 'Nama transaksi belum diisi.',
                transactioncode : 'Kode transaksi belum diisi.'
            },
            errorPlacement: function(error, element) {
                if (element.attr('name') == 'journaldate') {
                    error.appendTo('#journaldate-error-area');
                }else{
                    error.insertAfter(element);
                }
            },
            
        });

        current_form.on('change', '.journalaccountid', function(e) {
            const current_content = $('#' + $(this).closest('.form-input').attr('id'));

            current_content.find('.debet').prop('readonly', false);
            current_content.find('.credit').prop('readonly', false);
            current_content.find('.detailnote').prop('readonly', false);

            if (e.removed == null) {
                current_form.find('#btn-append').prop('disabled', false);
                current_form.find('button[type="submit"]').prop('disabled', false);
            }
        });

        current_form.submit(function() {
            current_form.find('.debet').unmask();
            current_form.find('.credit').unmask();
        });

        initUpdated();
    });

    function newDetail() {
        const btn_id = parseInt(current_form.find('#btn-append').attr('data-id')) + 1;
        const new_id = 'input-content-' + btn_id.toString();

        current_form.find('#btn-append').attr('data-id', btn_id.toString());
        $('#form-input-template').clone().show().appendTo('#input-content').attr('id', new_id);

        const current_content = current_form.find('#' + new_id);
        current_content.find('.journalaccountid').attr('id', 'journalaccountid-' + btn_id.toString());
        current_content.find('.debet').attr('id', 'debet-' + btn_id.toString());
        current_content.find('.credit').attr('id', 'credit-' + btn_id.toString());
        current_content.find('.detailnote').attr('id', 'detailnote-' + btn_id.toString());

        current_content.find('#journalaccountid-' + btn_id.toString()).select2({
            placeholder: 'Pilih Akun'
        });

        current_content.find('.debet').mask("#.##0.##0.##0.##0.##0.##0", {reverse: true});
        current_content.find('.credit').mask("#.##0.##0.##0.##0.##0.##0", {reverse: true});
        
        current_form.find('#btn-append').prop('disabled', true);
        current_form.find('button[type="submit"]').prop('disabled', true);
    }

    function changeOutlet() {
        const value = form_outlet.find('#outlet').val();
        current_form.find('#outlet').val(value);
    }

    function initUpdated(){
        const newRowCount = parseInt(rowCount);
        if (parseInt(newRowCount) > 0) {
            for (var i = 0; i < newRowCount; i++) {
                const current_content = current_form.find('#input-content #input-content-'+(i + 1));
                current_content.find('.journalaccountid').select2({
                    placeholder: 'Pilih Akun'
                });
                current_content.find('.debet').mask("#.##0.##0.##0.##0.##0.##0", {reverse: true});
                current_content.find('.credit').mask("#.##0.##0.##0.##0.##0.##0", {reverse: true});
            }
        }else{
            newDetail();
        }
    }

</script>