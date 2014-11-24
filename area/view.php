<?php
require_once('../inc.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ISA6120 - Advanced Database System - Assignment #1</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
    <script type="text/javascript"
      src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing"></script>
    <script type="text/javascript" src="wicket.js"></script>
    <script type="text/javascript" src="wicket-gmap3.js"></script>
    <script type="text/javascript" src="view.js"></script>
  </head>

  <body onload="app.gmap=app.init();">
    <div class="container" style="margin-top:20px;">
      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-12">
          <h3>Customized Area Overview <a class="btn btn-xs btn-primary" href="edit-wkt.html">Create</a> <a class="btn btn-xs btn-info" href="../index.php">Back</a></h3>
          <div id="canvas" style="width:100%;height:450px;"></div>
          <textarea style="display:none;" id="wkt"><?php echo getAllCustomAreaWKT(); ?></textarea>
        </div><!--/.col-xs-12.col-sm-9-->

      </div>

      <hr>

      <footer>
        <p>&copy; National Tsing Hua University, ISA6120 - Advanced Database System - Assignment #1 (Adeline, Kojima)</p>
      </footer>

    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script type="text/javascript">

   			$("#submitbutton").click( function(){
   				console.log($('#wkt').serialize());
   				$.ajax({
   					url: "area.php",
                	data: $('#wkt').serialize(),
                	type:"POST",
                	dataType:'text',
                	success: function(msg){
                    	alert(msg);
                    	window.location = "../index.html";
                	}
   				});

   			});
    </script>
  </body>
</html>

