<!DOCTYPE html>
<html>
  <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; width:100%; margin: 0; padding:0 }
      #map_canvas { height: 100%; width: 70%; float: left }
      #panels { height: 100%; width: 30%; float: right }
      .products { color: #0000FF }
      .reviews { display: none; color: #0000FF } /* 產品詳細資料一開始不顯示 */
    </style>
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyA2SijSHw48ozVyXnNFr-QI4alZe9RaFQE&sensor=FALSE">
    </script>
    <script type="text/javascript">
      window.onload = function () {

        var mapOptions = {
          center: new google.maps.LatLng(23.973875, 120.982024),  // 臺灣南投縣埔里鎮經緯度
          zoom: 4,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);

        google.maps.event.addListener(map, 'click', function(event) {
          //console.log("click:"+event.latLng);
          $.ajax({
            type: "GET",
            url: "query.php",
            data: {content: JSON.stringify(event)},
            dataType: "JSON", // return type
            success: function(msg) {
              var numProducts = msg.length; // 計算有多少個產品
              //var cid = msg.cid;
              //console.log(cid);
              $('.products').remove();  // 用.empty()清不掉
              $('.reviews').remove();

              $.each(msg, function(){ // Parse JSON
                var cid = this.cid;
                var pid = this.pid;
                $('#panels').append('<div id="' + pid + '" class="products">' + this.name);
                $('#'+pid).append('<div id="child' + pid + '" class="reviews">');
                
                $.each(this.contents, function(){ // 印出此產品所有評論
                  $('#child'+pid).append(this.content+'<br>');
                });
                // 這裡放Form，讓使用者輸入評論
                $('#child'+pid).append('</div>');
                $('#child'+pid).append('<form>Reviews: <input id="' + cid + '_' + pid + '" type="text" />\
                  <button>Submit</button></form>');
                // 結束Form
                //$('#child'+pid).append('</div>');
                $('#'+pid).append('</div>');
              });
              $('.products').click(function(){  //如果Product-name被點，詳細資訊要展開或收起
                var panelChild = $(this).children().attr('id');
                  $('#'+panelChild).toggle();
              });
              $('.reviews').click(function(e){  // 點評論內的文字時，不會呼叫toggle()
                e.stopPropagation();
              });
              $('form').submit(function(e){
                e.preventDefault();
                var formClicked = $(this).children().attr('id');
                $.ajax({
                  type: "GET",
                  url: "review.php",
                  data: {content2: formClicked, content3:$('#'+formClicked).val()}, // 傳送cid, pid, review
                  dataType: "text", // return type
                  success: function(msg2) {
                    //var clicked = formClicked.split('_');
                    //$('#child'+clicked[1]).append($('#'+formClicked).val());
                    $('#'+formClicked).val(''); // submit後重設input
                  }
                });
              });
            }
          });
        });
      };
    </script>
  </head>
  <body>
    <div id="map_canvas"></div>
    <div id="panels"></div>
  </body>
</html>