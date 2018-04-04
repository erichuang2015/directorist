<?php
if (!empty($args['listing_info'])) { extract($args['listing_info']); }


//@TODO: I will have to add a text area to get the content for the info window of the map later
$t = get_the_title();
$t = !empty($t)? $t : __('No Title', ATBDP_TEXTDOMAIN);
$tg = !empty($tagline)? esc_html($tagline) : '';
$ad = !empty($address)? esc_html($address) : '';
$image = (!empty($attachment_id[0])) ? "<img src='". esc_url(wp_get_attachment_image_url($attachment_id[0], 'thumbnail'))."'>": '';
$info_content = "<div class='map_info_window'> <h3>{$t}</h3>";
$info_content .= "<p> {$tg}</p>";
$info_content .= $image ; // add the image if available
$info_content .= "<p> {$ad}</p></div>";

// grab social information
$social_info = !empty( $social ) ? $social : array();
$map_zoom_level = get_directorist_option('map_zoom_level', 16);
$disable_map = get_directorist_option('disable_map');

?>
<div class="directorist directory_wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="add_listing_form_wrapper">
                    <?php
                    /**
                     * It fires before the listing tagline
                     * @param string $type Page type.
                     * @param array $args Current listing details.
                     * @since 1.1.1
                     **/
                    do_action('atbdp_edit_before_tagline_fields', 'add_listing_page_backend', $args['listing_info']);
                    ?>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="atbd_excerpt"><?php esc_html_e('Tag-line/Motto', ATBDP_TEXTDOMAIN) ?></label>
                                <input type="text" name="listing[tagline]" value="<?= !empty($tagline) ? esc_attr($tagline): ''; ?>" class="form-control directory_field" placeholder="<?= __('Your Organization\'s motto or tag-line', ATBDP_TEXTDOMAIN); ?>"/>
                            </div>
                            <div class="form-group">
                                <label for="atbd_excerpt"><?php esc_html_e('Short Description/Excerpt:', ATBDP_TEXTDOMAIN) ?></label>
                                <textarea name="listing[excerpt]" id="atbd_excerpt"  class="form-control directory_field" cols="30" rows="5" placeholder="<?= __('Short Description or Excerpt', ATBDP_TEXTDOMAIN); ?>"><?= !empty($excerpt) ?esc_textarea( stripslashes($excerpt)): ''; ?></textarea>
                            </div>

                        </div>
                    </div>






                    <div class="row">
                        <!-- MAP or ADDRESS related information starts here -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="address"><?php esc_html_e('Address:', ATBDP_TEXTDOMAIN); ?></label>
                                <input type="text" name="listing[address]" id="address" value="<?= !empty($address) ? esc_attr($address): ''; ?>" class="form-control directory_field" placeholder="<?php esc_html_e('Listing address eg. Houghton Street London WC2A 2AE UK', ATBDP_TEXTDOMAIN); ?>"/>
                            </div>
                        </div>
                        <!--phone-->
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="atbdp_phone_number"><?php esc_html_e('Phone Number:', ATBDP_TEXTDOMAIN); ?></label>
                                <input type="tel" name="listing[phone][]" id="atbdp_phone_number" value="<?= !empty($phone[0]) ? esc_attr($phone[0]): ''; ?>" class="form-control directory_field" placeholder="Phone Number"/>
                            </div>

                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="atbdp_email"><?php esc_html_e('Email:', ATBDP_TEXTDOMAIN); ?></label>
                                <input type="email" name="listing[email]" id="atbdp_email" value="<?= !empty( $email ) ? esc_attr($email) : ''; ?>" class="form-control directory_field" placeholder="Enter Email"/>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="atbdp_website"><?php esc_html_e('Website:', ATBDP_TEXTDOMAIN); ?></label>

                                <input type="text" id="atbdp_website" name="listing[website]" value="<?= !empty( $website ) ? esc_url($website) : ''; ?>" class="form-control directory_field" placeholder="Listing website eg. http://example.com"/>
                            </div>
                        </div>
                    </div> <!--ends .row-->

                    <!--Social Information-->

                    <?php
                        /**
                         * It fires before social information fields
                         * @param string $type Page type.
                         * @param array $listing_info Information of the current listing
                         * @since 1.1.1
                         **/
                        do_action('atbdp_edit_before_social_info_fields', 'add_listing_page_backend', $args['listing_info']);

                        ATBDP()->load_template('meta-partials/social', array('social_info' => $social_info));

                        /**
                         * It fires after social information fields
                         * @param string $type Page type.
                         * @param array $listing_info Information of the current listing
                         * @since 1.1.1
                         **/
                        do_action('atbdp_edit_after_social_info_fields', 'add_listing_page_backend', $args['listing_info']);
                    ?>



                    <?php if (!$disable_map) { ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="cor-wrap">
                                    <input type="checkbox" name="listing[manual_coordinate]" value="1"
                                           id="manual_coordinate" <?= (!empty($manual_coordinate)) ? 'checked' : ''; ?> >
                                    <label for="manual_coordinate"> <?php _e('Enter Coordinates ( latitude and longitude) Manually ? or set the marker on the map anywhere by clicking on the map', ATBDP_TEXTDOMAIN); ?> </label>
                                </div>
                            </div>

                            <div id="hide_if_no_manual_cor">

                                <div class="col-md-5 col-sm-12 v_middle">
                                    <div class="form-group">
                                        <label for="manual_lat"> <?php _e('Latitude', ATBDP_TEXTDOMAIN); ?>  </label>
                                        <input type="text" name="listing[manual_lat]" id="manual_lat"
                                               value="<?= (!empty($manual_lat)) ? $manual_lat : '' ?>"
                                               class="form-control directory_field"
                                               placeholder="Enter Latitude eg. 24.89904"/>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12 v_middle">
                                    <div class="form-group">
                                        <label for="manual_lng"> <?php _e('Longitude', ATBDP_TEXTDOMAIN); ?> </label>
                                        <input type="text" name="listing[manual_lng]" id="manual_lng"
                                               value="<?= (!empty($manual_lng)) ? $manual_lng : '' ?>"
                                               class="form-control directory_field"
                                               placeholder="Enter Longitude eg. 91.87198"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12 v_middle">
                                    <div class="lat_btn_wrap">
                                        <button class="btn btn-default btn-sm"
                                                id="generate_admin_map"><?php _e('Generate on Map', ATBDP_TEXTDOMAIN); ?></button>
                                    </div>
                                </div> <!-- ends #hide_if_no_manual_cor-->


                            </div> <!--ends #hide_if_no_manual_cor -->
                        </div> <!--ends .row-->


                               <!--Google map will be generated here using js-->
                        <div class="map_wrapper">
                            <div id="floating-panel">
                                <button class="btn btn-danger"
                                        id="delete_marker"> <?php _e('Delete Marker', ATBDP_TEXTDOMAIN); ?></button>
                            </div>

                            <div id="gmap"></div>
                        </div>

                        <?php
                        /**
                         * It fires after the google map preview area
                         * @param string $type Page type.
                         * @param array $listing_info Information of the current listing
                         * @since 1.1.1
                         **/
                        do_action('atbdp_edit_after_googlemap_preview', 'add_listing_page_backend', $args['listing_info']);
                    }
                    ?>
                </div><!--ends add_listing_form_wrapper-->

            </div> <!--ends col-md-12 -->
        </div><!--ends .row-->
    </div> <!--ends container-fluid-->
</div>
<script>

    // Bias the auto complete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.

    jQuery(document).ready(function ($) {
        <?php if (!$disable_map) { ?>

        // initialize all vars here to avoid hoisting related misunderstanding.
        var placeSearch, map, autocomplete, address_input, markers, info_window, $manual_lat, $manual_lng, saved_lat_lng, info_content;
         $manual_lat = $('#manual_lat');
         $manual_lng = $('#manual_lng');
         saved_lat_lng = {lat:<?= (!empty($manual_lat)) ? floatval($manual_lat) : '51.5073509' ?>, lng: <?= (!empty($manual_lng)) ? floatval($manual_lng) : '-0.12775829999998223' ?> }; // default is London city
        info_content = "<?= $info_content; ?>";
        markers = [];// initialize the array to keep track all the marker
        /*@todo; make the max width size customizable*/
         info_window = new google.maps.InfoWindow({
            content: info_content,
             maxWidth: 400
        });



        address_input = document.getElementById('address');
        address_input.addEventListener('focus', geolocate);
        // this function will work on sites that uses SSL, it applies to Chrome especially, other browsers may allow location sharing without securing.
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }


        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
                (address_input),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the necessary input fields and draw a marker
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            // set the value of input field to save them to the database
            $manual_lat.val(place.geometry.location.lat());
            $manual_lng.val(place.geometry.location.lng());
            map.setCenter(place.geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location
            });

            marker.addListener('click', function() {
                info_window.open(map, marker);
            });

            // add the marker to the markers array to keep track of it, so that we can show/hide/delete them all later.
            markers.push(marker);
        }

        initAutocomplete(); // start google map place auto complete API call


        function initMap() {
            /* Create new map instance*/
                map = new google.maps.Map(document.getElementById('gmap'), {
                zoom: <?php echo !empty($map_zoom_level) ? intval($map_zoom_level) : 16; ?>,
                center: saved_lat_lng
            });
             var marker = new google.maps.Marker({
                 map: map,
                 position:  saved_lat_lng,
                 draggable:true,
                 title: '<?php _e('You can drag the marker to your desired place to place a marker', ATBDP_TEXTDOMAIN); ?>'
             });
            marker.addListener('click', function() {
                info_window.open(map, marker);
            });
             // add the marker to the markers array to keep track of it, so that we can show/hide/delete them all later.
            markers.push(marker);

            // create a Geocode instance
            var geocoder = new google.maps.Geocoder();

            document.getElementById('generate_admin_map').addEventListener('click', function(e) {
                e.preventDefault();
                geocodeAddress(geocoder, map);
            });


            // This event listener calls addMarker() when the map is clicked.
            google.maps.event.addListener(map, 'click', function(event) {
                // at first delete the old marker if there is any and then add new marker
                deleteMarker();
                // set the value of input field to save them to the database
                $manual_lat.val(event.latLng.lat());
                $manual_lng.val(event.latLng.lng());
                // add the marker to the given map.
                addMarker(event.latLng, map);
            });

            // This event listener update the lat long field of the form so that we can add the lat long to the database when the MARKER is drag.
            google.maps.event.addListener(marker, 'dragend', function(event) {
                // set the value of input field to save them to the database
                $manual_lat.val(event.latLng.lat());
                $manual_lng.val(event.latLng.lng());
            });
        }

        /*
        * Geocode and address using google map javascript api and then populate the input fields for storing lat and long
        * */

        function geocodeAddress(geocoder, resultsMap) {
            var address = address_input.value;
            geocoder.geocode({'address': address}, function(results, status) {
                //console.dir(results);
                if (status === 'OK') {
                    // set the value of input field to save them to the database
                    $manual_lat.val(results[0].geometry.location.lat());
                    $manual_lng.val(results[0].geometry.location.lng());
                    resultsMap.setCenter(results[0].geometry.location);
                     var marker = new google.maps.Marker({
                        map: resultsMap,
                        position: results[0].geometry.location
                    });

                    marker.addListener('click', function() {
                        info_window.open(map, marker);
                    });

                    // add the marker to the markers array to keep track of it, so that we can show/hide/delete them all later.
                    markers.push(marker);
                } else {
                    alert( '<?php _e('Geocode was not successful for the following reason: ', ATBDP_TEXTDOMAIN); ?>' + status);
                }
            });
        }

        initMap();



        // adding features of creating marker manually on the map on add listing page.
        /*var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var labelIndex = 0;*/


        // Adds a marker to the map.
        function addMarker(location, map) {
            // Add the marker at the clicked location, and add the next-available label
            // from the array of alphabetical characters.
             var marker = new google.maps.Marker({
                position: location,
                /*label: labels[labelIndex++ % labels.length],*/
                 draggable:true,
                 title: '<?php _e('You can drag the marker to your desired place to place a marker', ATBDP_TEXTDOMAIN); ?>',
                map: map
            });
            marker.addListener('click', function() {
                info_window.open(map, marker);
            });
            // add the marker to the markers array to keep track of it, so that we can show/hide/delete them all later.
            markers.push(marker);
        }

        // Delete Marker
        $('#delete_marker').on('click', function (e) {
            e.preventDefault();
            deleteMarker();// delete all markers

        });
        /**
         * It deletes all the map markers
         * */
        function deleteMarker() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }
        <?php } ?>



    }); // ends jquery ready function.







</script>