      var currentPID;
      var currentCID;
      var currentCurrency;
      var currentPrice;

    function initialize() {

      var mapProp = {
        center: new google.maps.LatLng(23.973875, 120.982024),
        zoom: 4,
        maxZoom: 4,
        minZoom: 2,
        streetViewControl: false,
        mapTypeControl: false,
        mapTypeId:google.maps.MapTypeId.ROADMAP
      };
      var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

      var marker = new google.maps.Marker({
        position: map.getCenter(),
        map: map,
        title: 'I\'m Here!',
        draggable: true
      });

      google.maps.event.addListener(marker, 'dragend', function(event) {
        console.log("Marker click:"+event.latLng);
        map.setCenter(marker.getPosition());

        // Passing location to get results - Start
        $.ajax({
          type: "GET",
          url: "query.php",
          data: {content: JSON.stringify(event)},
          dataType: "JSON", // return type
          
          success: function(ret) {
            console.log(ret);
            $("#applist-content").html("");
            if(ret.country == null){
              $("#position").html("Empty");
            }
            else{
              currentCID = ret.country.ISO3;
              $("#position").html(ret.country.name);
              for (i =0; i< ret.apps.length; i++) {
                //$("#applist-content").append(ret.apps[i].name);
                if (i % 5 == 0)
                  $("#applist-content").append("<div class=\"col-xs-3 col-lg-2 col-lg-offset-1\">\
    <a href=\"#\" class=\"modalButton\" data-toggle=\"modal\" data-target=\"#myModal\" data-title=\""+ret.apps[i].name+"\" data-icon=\""+ret.apps[i].icon+"\" data-src=\""+ret.apps[i].pid+"\" data-price=\""+ret.apps[i].price+"\" data-currency=\""+ret.apps[i].currency+"\" data-href=\""+ret.apps[i].url+"\" data-star=\""+ret.apps[i].average+"\">\
    <img src=\""+ret.apps[i].icon+"\" style=\"height:60px;width:60px;\" class=\"img-rounded\"/>\
    <h5>"+ret.apps[i].name+"</h5></a>\
  </div>");
                else
                  $("#applist-content").append("<div class=\"col-xs-3 col-lg-2\">\
    <a href=\"#\" class=\"modalButton\" data-toggle=\"modal\" data-target=\"#myModal\" data-title=\""+ret.apps[i].name+"\" data-icon=\""+ret.apps[i].icon+"\" data-src=\""+ret.apps[i].pid+"\" data-price=\""+ret.apps[i].price+"\" data-currency=\""+ret.apps[i].currency+"\" data-href=\""+ret.apps[i].url+"\" data-star=\""+ret.apps[i].average+"\">\
    <img src=\""+ret.apps[i].icon+"\" style=\"height:60px;width:60px;\" class=\"img-rounded\"/>\
    <h5>"+ret.apps[i].name+"</h5></a>\
  </div>");
              }
              $("a.modalButton").on('click', function(e){
                var title = $(this).attr('data-title');
                var icon = $(this).attr('data-icon');
                var price = $(this).attr('data-price');
                var currency = $(this).attr('data-currency');
                var id = $(this).attr('data-src');
                var url = $(this).attr('data-href');
                var star = $(this).attr('data-star');
                currentPID = id;
                currentCurrency = currency;
                currentPrice = price;
                $("#input-id").rating({'step': 1.0, 'size':'md'});
                //$("#myModal .modal-body").html("Something");
                $("#myModal .modalLink").attr("href", url);
                $("#myModal .modalBadge").html(currency+": "+price+" Avg: "+star);
                $("#myModal .modalTitle").html(title);
                $("#myModal .modalID").val(id);
                $("#myModal .modalImage").attr("src", icon);
              });
            }
          }
        });
        // 使用者點擊星星
        $('#input-id').on('rating.change', function(event, value, caption){
          $.ajax({
          type: "GET",
          url: "star.php",
          data: {star: value, country: currentCID, pid: currentPID},
          dataType: "text", // return type
          success: function(ret2){
              console.log(ret2);
              $("#myModal .modalBadge").html(currentCurrency+": "+currentPrice+" Avg: "+ret2);
              $("a.modalButton").attr('data-star', ret2);
            }
          });
        });
        // Passing location to get results - End
      });

    }
    google.maps.event.addDomListener(window, 'load', initialize);