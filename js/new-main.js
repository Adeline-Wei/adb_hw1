var app = (function () {
    return {
        features: [],
        /**
         * Clears the map contents.
         */
      clearMap: function () {
          var i;

          // Reset the remembered last string (so that we can clear the map,
          //  paste the same string, and see it again)
          document.getElementById('wkt').last = '';

          for (i in this.features) {
              if (this.features.hasOwnProperty(i)) {
                  this.features[i].setMap(null);
              }
          }
          this.features.length = 0;
      },
        /**
         * Clears the current contents of the textarea.
         */
  	  clearText: function () {
  	      document.getElementById('wkt').value = '';
  	  },
        /**
         * Maps the current contents of the textarea.
         * @return  {Object}    Some sort of geometry object
         */
        mapIt: function () {
            var el, obj, wkt;

            el = document.getElementById('wkt');
            wkt = new Wkt.Wkt();

            if (el.last === el.value) { // Remember the last string
                return; // Do nothing if the WKT string hasn't changed
            } else {
                el.last = el.value;
            }

            try { // Catch any malformed WKT strings
                wkt.read(el.value);
            } catch (e1) {
                try {
                    wkt.read(el.value.replace('\n', '').replace('\r', '').replace('\t', ''));
                } catch (e2) {
                    if (e2.name === 'WKTError') {
                        alert('Wicket could not understand the WKT string you entered. Check that you have parentheses balanced, and try removing tabs and newline characters.');
                        return;
                    }
                }
            }

            obj = wkt.toObject(this.gmap.defaults); // Make an object

            // Add listeners for overlay editing events
            if (wkt.type === 'polygon' || wkt.type === 'linestring') {
                // New vertex is inserted
                google.maps.event.addListener(obj.getPath(), 'insert_at', function (n) {
                    app.updateText();
                });
                // Existing vertex is removed (insertion is undone)
                google.maps.event.addListener(obj.getPath(), 'remove_at', function (n) {
                    app.updateText();
                });
                // Existing vertex is moved (set elsewhere)
                google.maps.event.addListener(obj.getPath(), 'set_at', function (n) {
                    app.updateText();
                });
            } else {
                if (obj.setEditable) {obj.setEditable(false);}
            }

          if (Wkt.isArray(obj)) { // Distinguish multigeometries (Arrays) from objects
              for (i in obj) {
                  if (obj.hasOwnProperty(i) && !Wkt.isArray(obj[i])) {
                      obj[i].setMap(this.gmap);
                      this.features.push(obj[i]);
                  }
              }
          } else {
              obj.setMap(this.gmap); // Add it to the map
              this.features.push(obj);
          }

            // Pan the map to the feature
            if (obj.getBounds !== undefined && typeof obj.getBounds === 'function') {
                // For objects that have defined bounds or a way to get them
                this.gmap.fitBounds(obj.getBounds());
            } else {
                if (obj.getPath !== undefined && typeof obj.getPath === 'function') {
                // For Polygons and Polylines
                this.gmap.panTo(obj.getPath().getAt(0));
                } else { // But points (Markers) are different
                    if (obj.getPosition !== undefined && typeof obj.getPosition === 'function') {
                        this.gmap.panTo(obj.getPosition());
                    }
                }
            }


            return obj;
        },
        /**
         * Updates the textarea based on the first available feature.
         */
        updateText: function () {
            var wkt = new Wkt.Wkt();
            wkt.fromObject(this.features[0] );
            document.getElementById('wkt').value = wkt.write();
        },
        /**
         * Application entry point.
         * @return  {<google.maps.Map>} The Google Maps API instance
         */
        init: function () {
            var gmap, marker;

            gmap = new google.maps.Map(document.getElementById('canvas'), {
                center: new google.maps.LatLng(30, 10),
                defaults: {
                    icon: 'red_dot.png',
                    shadow: 'dot_shadow.png',
                    editable: false,
                    strokeColor: '#990000',
                    fillColor: '#EEFFCC',
                    fillOpacity: 0.6
                },
                disableDefaultUI: true,
                mapTypeControl: false,
                panControl: true,
                streetViewControl: false,
                zoom: 2,
                maxZoom: 4,
                minZoom: 2,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_TOP
                }
            });

            marker = new google.maps.Marker({
              position: gmap.getCenter(),
              map: gmap,
              title: 'I\'m Here!',
              draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                    console.log("Marker click:"+event.latLng);
                    gmap.setCenter(marker.getPosition());

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
                    $("a[data-src="+currentPID+"]").attr('data-star', ret2);
                  }
                });
              });
              // Passing location to get results - End
            });

            google.maps.event.addListener(gmap, 'tilesloaded', function () {
                if (!this.loaded) {
                    this.loaded = true;
                    // NOTE: We start with a MULTIPOLYGON; these aren't easily deconstructed, so we won't set this object to be     editable in this example
                    //document.getElementById('wkt').value = 'MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)), ((20 35, 45 20, 30 5,     10 10, 10 30, 20 35), (30 20, 20 25, 20 15, 30 20)))';
                    app.mapIt();
                }
            });
            gmap.drawingManager = new google.maps.drawing.DrawingManager({
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: []
                },
                drawingControl: false,
                polygonOptions: gmap.defaults,
                rectangleOptions: gmap.defaults
            });
            gmap.drawingManager.setMap(gmap);
            google.maps.event.addListener(gmap.drawingManager, 'overlaycomplete', function (event) {
            var wkt;

            app.clearText();
              app.clearMap();

            // Set the drawing mode to "pan" (the hand) so users can immediately edit
              this.setDrawingMode(null);

            // Polygon drawn
              if (event.type === google.maps.drawing.OverlayType.POLYGON || event.type === google.maps.drawing.OverlayType.    POLINE) {
                  // New vertex is inserted
                  google.maps.event.addListener(event.overlay.getPath(), 'insert_at', function (n) {
                      app.updateText();
                  });

                // Existing vertex is removed (insertion is undone)
                  google.maps.event.addListener(event.overlay.getPath(), 'remove_at', function (n) {
                      app.updateText();
                  });

                // Existing vertex is moved (set elsewhere)
                  google.maps.event.addListener(event.overlay.getPath(), 'set_at', function (n) {
                      app.updateText();
                  });
              } else if (event.type === google.maps.drawing.OverlayType.RECTANGLE) { // Rectangle drawn
                  // Listen for the 'bounds_changed' event and update the geometry
                  google.maps.event.addListener(event.overlay, 'bounds_changed', function () {
                      app.updateText();
                  });
              }

              app.features.push(event.overlay);
              wkt = new Wkt.Wkt();
              wkt.fromObject(event.overlay);
              document.getElementById('wkt').value = wkt.write();
              });

        return gmap;
      }
  };
}()); // Execute immediatel