<?php
/**
* Created by PhpStorm.
* User: Husnan
* Date: 08/12/2015
* Time: 14:59
*/
?>

<script type="text/javascript">
function switchUserAksesOnOffChanged(inputcheckbox) {
    var $ = jQuery();
    var ischecked = inputcheckbox.checked;
    var kol = inputcheckbox.getAttribute('data-tag');
    var nama = inputcheckbox.getAttribute('id');
    var username = '<?=$selectedusername;?>';
    // if(kol == "ItemView" && ischecked) {
    //     document.getElementById('divItemAdd').style.display = "block";
    //     document.getElementById('divItemEdit').style.display = "block";
    //     document.getElementById('divItemDelete').style.display = "block";
    // } else if(kol == "ItemView" && !ischecked) {
    //     document.getElementById('divItemAdd').style.display = "none";
    //     document.getElementById('divItemEdit').style.display = "none";
    //     document.getElementById('divItemDelete').style.display = "none";
    // }
    //
    // if(kol == "ProdukView" && ischecked) {
    //     document.getElementById('divProdukAdd').style.display = "block";
    //     document.getElementById('divProdukEdit').style.display = "block";
    //     document.getElementById('divProdukDelete').style.display = "block";
    // } else if(kol == "ProdukView" && !ischecked) {
    //     document.getElementById('divProdukAdd').style.display = "none";
    //     document.getElementById('divProdukEdit').style.display = "none";
    //     document.getElementById('divProdukDelete').style.display = "none";
    // }
    //
    // if(kol == "SupplierView" && ischecked) {
    //     document.getElementById('divSupplierAdd').style.display = "block";
    //     document.getElementById('divSupplierEdit').style.display = "block";
    //     document.getElementById('divSupplierDelete').style.display = "block";
    // } else if(kol == "SupplierView" && !ischecked) {
    //     document.getElementById('divSupplierAdd').style.display = "none";
    //     document.getElementById('divSupplierEdit').style.display = "none";
    //     document.getElementById('divSupplierDelete').style.display = "none";
    // }
    //
    // if(kol == "DataRekeningView" && ischecked) {
    //     document.getElementById('divDataRekeningAdd').style.display = "block";
    //     document.getElementById('divDataRekeningEdit').style.display = "block";
    //     document.getElementById('divDataRekeningDelete').style.display = "block";
    // } else if(kol == "DataRekeningView" && !ischecked) {
    //     document.getElementById('divDataRekeningAdd').style.display = "none";
    //     document.getElementById('divDataRekeningEdit').style.display = "none";
    //     document.getElementById('divDataRekeningDelete').style.display = "none";
    // }
    //
    // if(kol == "CustomerView" && ischecked) {
    //     document.getElementById('divCustomerAdd').style.display = "block";
    //     document.getElementById('divCustomerEdit').style.display = "block";
    //     document.getElementById('divCustomerDelete').style.display = "block";
    // } else if(kol == "CustomerView" && !ischecked) {
    //     document.getElementById('divCustomerAdd').style.display = "none";
    //     document.getElementById('divCustomerEdit').style.display = "none";
    //     document.getElementById('divCustomerDelete').style.display = "none";
    // }
    //
    // if(kol == "PurchaseView" && ischecked) {
    //     document.getElementById('divPurchaseAdd').style.display = "block";
    //     document.getElementById('divPurchaseEdit').style.display = "block";
    //     document.getElementById('divPurchaseDelete').style.display = "block";
    // } else if(kol == "PurchaseView" && !ischecked) {
    //     document.getElementById('divPurchaseAdd').style.display = "none";
    //     document.getElementById('divPurchaseEdit').style.display = "none";
    //     document.getElementById('divPurchaseDelete').style.display = "none";
    // }
    //
    // if(kol == "StockView" && ischecked) {
    //     document.getElementById('divStockAdd').style.display = "block";
    //     document.getElementById('divStockEdit').style.display = "block";
    //     document.getElementById('divStockDelete').style.display = "block";
    // } else if(kol == "StockView" && !ischecked) {
    //     document.getElementById('divStockAdd').style.display = "none";
    //     document.getElementById('divStockEdit').style.display = "none";
    //     document.getElementById('divStockDelete').style.display = "none";
    // }
    //
    // if(kol == "MoneyView" && ischecked) {
    //     document.getElementById('divMoneyAdd').style.display = "block";
    //     document.getElementById('divMoneyEdit').style.display = "block";
    //     document.getElementById('divMoneyDelete').style.display = "block";
    // } else if(kol == "MoneyView" && !ischecked) {
    //     document.getElementById('divMoneyAdd').style.display = "none";
    //     document.getElementById('divMoneyEdit').style.display = "none";
    //     document.getElementById('divMoneyDelete').style.display = "none";
    // }
    jQuery.post("<?=base_url().'ajax/updateaksescloud';?>", {
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
