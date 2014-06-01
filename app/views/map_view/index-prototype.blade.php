<html>
    <head>
        <title></title>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script type="text/javascript">
            /* 
             * Draggable Map Marker code from http://www.smipple.net/snippet/sulhas/Example%20Draggable%20Marker%20Google%20Maps
             */
            var geocoder = new google.maps.Geocoder();

            function geocodePosition(pos) {
                geocoder.geocode({
                    latLng: pos
                }, function(responses) {
                    if (responses && responses.length > 0) {
                        updateMarkerAddress(responses[0].formatted_address);
                    } else {
                        updateMarkerAddress('Cannot determine address at this location.');
                    }
                });
            }

            function updateMarkerStatus(str) {
                document.getElementById('markerStatus').innerHTML = str;
            }

            function updateMarkerPosition(latLng) {
                document.getElementById('info').innerHTML = [
                    latLng.lat(),
                    latLng.lng()
                ].join(', ');
            }

            function updateMarkerAddress(str) {
                document.getElementById('address').innerHTML = str;
            }

            function initialize(position) {
                var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                var map = new google.maps.Map(document.getElementById('mapCanvas'), {
                    zoom: 8,
                    center: latLng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var marker = new google.maps.Marker({
                    position: latLng,
                    title: 'Point A',
                    map: map,
                    draggable: true
                });

                // Update current position info.
                updateMarkerPosition(latLng);
                geocodePosition(latLng);

                // Add dragging event listeners.
                google.maps.event.addListener(marker, 'dragstart', function() {
                    updateMarkerAddress('Dragging...');
                });

                google.maps.event.addListener(marker, 'drag', function() {
                    updateMarkerStatus('Dragging...');
                    updateMarkerPosition(marker.getPosition());
                });

                google.maps.event.addListener(marker, 'dragend', function() {
                    updateMarkerStatus('Drag ended');
                    geocodePosition(marker.getPosition());
                });
            }

            function initializeDefault() {
                var defaultPosition = {
                    coords: {
                        latitude: 39.739341754525086,
                        longitude: -104.98478651046753
                    }
                }

                initialize(defaultPosition);
            }

            function loadGeolocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(initialize, initializeDefault);
                } else {
                    // no geolocation service
                    initializeDefault();
                }
            }

            // Onload handler to fire off the app.
            google.maps.event.addDomListener(window, 'load', loadGeolocation);
        </script>
        <script type="text/javascript">
            var messages = {};
            var messageIndex = 1;

            function postMessage(message) {
                messages[messageIndex++] = {
                    text: message,
                    active: true
                };

                updateMessageView();
            }

            function removeMessage(id) {
                console.log('Triggered: removeMessage(', id, ')');
                console.log('Reverse this action with: addMessage(', id, ')');
                messages[id].active = false;
                updateMessageView();
            }

            function addMessage(id) {
                messages[id].active = true;
                updateMessageView();
            }

            function updateMessageView() {
                var domMessages = $('.messages');
                domMessages.children('li').addClass('hidden');

                var previousElement = null;
                $.each(messages, function(id, value) {
                    if(value.active) {
                        var element = $('#message-'+id);
                        if (element.size()) {
                            element.removeClass('hidden');
                        } else {
                            var newElement = $('<li>').attr('id','message-'+id).data('id',id).html(value.text);
                            if (previousElement) {
                                previousElement.after(newElement);
                            } else {
                                domMessages.prepend(newElement);
                            }
                        }
                        previousElement = element;
                    }
                });
            }

            $(document).on('submit', '#chat form', function(e) {
                e.preventDefault();
                var input = $(this).find('input');
                postMessage(input.val());
                input.val('');
            }).on('click', '#chat .messages > li', function(e) {
                var id = $(this).data('id');
                removeMessage(id);
            });
        </script>
        <style type="text/css">
            * {
                box-sizing: border-box;
            }
            body {
                height: 100%;
                padding: 0;
                margin: 0;
            }
            #mapCanvas {
                height: 100%;
                margin-right: 300px;
            }
            #infoPanel {
                position: absolute;
                bottom: 10px;
                right: 310px;
                border-radius: 5px;
                background-color: white;
                padding: 9px;
                font-size: 12px;
            }
            #chat {
                position: relative;
                float: right;
                height: 100%;
                width: 300px;
            }
            #chat .messages {
                position: relative;
                overflow: auto;
                height: 100%;
                margin: 0;
                padding: 10px;
                padding-bottom: 50px;
            }
            #chat .messages > li {
                display: block;
                background: #ddeeb0;
                border-radius: 7px;
                margin: 3px;
                padding: 8px;
                opacity: 1;
                max-height: 150px;
                transition: all 0.5s;
                cursor: pointer;
            }
            #chat .messages > li.hidden {
                opacity: 0;
                max-height: 0;
                padding: 0;
                margin-bottom: -3px;
            }
            #chat form {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                padding: 10px;
                margin: 0;
                border-top: 1px solid lightgray;
                background: white;
            }
            #chat form input {
                width: 100%;
            }
            #infoPanel div {
                margin-bottom: 5px;
            }
        </style>
    </head>
    <body>
        <div id="chat">
            <ul class="messages"></ul>
            <form>
                <input type="text"/>
            </form>
        </div>
        <div id="mapCanvas"></div>
        <div id="infoPanel">
            <b>Marker status:</b>
            <div id="markerStatus"><i>Click and drag the marker.</i></div>
            <b>Current position:</b>
            <div id="info"></div>
            <b>Closest matching address:</b>
            <div id="address"></div>
        </div>
    </body>
</html>
