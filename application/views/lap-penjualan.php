<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url();?>js/jqgrid-themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url();?>js/jqgrid/css/ui.jqgrid.css"></link>	
 
	<script src="<?=base_url();?>js/jquery.min.js" type="text/javascript"></script>
	<script src="<?=base_url();?>js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="<?=base_url();?>js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="<?=base_url();?>js/jqgrid-themes/jquery-ui.custom.min.js" type="text/javascript"></script>
<script>
  // e.g. to show footer summary
  function grid_onload()
  {
  };
</script>
</head>
<body>

<form action="lap-penjualan.php">
  Periode: <input type="date" name="mulaitanggal" id="datemulai" value="<?php 
//echo date("Y/m/d"); 
if(!isset($_GET["mulaitanggal"])) echo date("Y/m/d"); 
else echo $dt1;
?>">
  - <input type="date" name="sampaitanggal" id="datesampai" value="<?php 
if(!isset($_GET["sampaitanggal"])) echo date("Y/m/d"); 
else echo $dt2;?>"><br>
  Outlet: <input type="text" name="outlet">
  <input type="submit" value="Refresh">
</form>
	<div style="margin:10px">
	<?php echo $table;?>
	</div>
</body>
</html>