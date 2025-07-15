@props(['latitudeField' => 'latitude', 'longitudeField' => 'longitude'])

<div>
    <div id="map" style="height: 400px; width: 100%;" class="mt-4"></div>

        <script>
            window.initMap = function() {
                const initialLat = parseFloat(document.getElementById('{{ $latitudeField }}').value) || 39.15;
                const initialLng = parseFloat(document.getElementById('{{ $longitudeField }}').value) || 22.3;

                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: initialLat, lng: initialLng },
                    zoom: 8,
                });

                const marker = new google.maps.Marker({
                    position: { lat: initialLat, lng: initialLng },
                    map: map,
                    draggable: true, 
                });

                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    document.getElementById('{{ $latitudeField }}').value = position.lat();
                    document.getElementById('{{ $longitudeField }}').value = position.lng();
                });

                document.getElementById('{{ $latitudeField }}').addEventListener('input', updateMarkerPosition);
                document.getElementById('{{ $longitudeField }}').addEventListener('input', updateMarkerPosition);

                function updateMarkerPosition() {
                    const newLat = parseFloat(document.getElementById('{{ $latitudeField }}').value) || initialLat;
                    const newLng = parseFloat(document.getElementById('{{ $longitudeField }}').value) || initialLng;
                    marker.setPosition({ lat: newLat, lng: newLng });
                    map.setCenter({ lat: newLat, lng: newLng });
                }
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
</div>
