<?php

/*
 *  UCFBands CPT Single Template: Event
 *    
 *  @author Jordan Pakrosnis
*/


// GOOGLE MAPS API
add_action( 'wp_enqueue_scripts', 'ucfbands_google_maps_api' );
function ucfbands_google_maps_api() {
    
    wp_enqueue_script( 'google-maps-api', 'http://maps.googleapis.com/maps/api/js', array(), CHILD_THEME_VERSION );
    
} // ucfbands_google_maps_api()



// REMOVE SIDEBAR, POST INFO, & META //
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );


// FULL-WIDTH CLASS
require_once( CHILD_DIR . '/inc/full-width-content.php' );




add_action( 'genesis_before_entry', 'ucfbands_event_single_data' );

/**
 * UCFBands Event - Get Post & Data
 *
 * @author Jordan Pakrosnis
 */
function ucfbands_event_single_data() {
    
    // Get Post ID
    $event = get_the_ID();
    
    // Get Event meta
    global $event_meta;
    $event_meta = ucfbands_event_get_meta( $event, true, true, true );
    
} // ucfbands_event_single_data() 




add_action( 'genesis_entry_header', 'ucfbands_event_single_date_badge', 5 );

/**
 * UCFBands Event - Date Badge for Single
 *
 * @author Jordan Pakrosnis
 */
function ucfbands_event_single_date_badge() {
    
    $event_meta = $GLOBALS["event_meta"];
    
    $event_date = ucfbands_event_date_badge(
        $event_meta['start_date_time'],
        $event_meta['finish_date_time'],
        $event_meta['icon_background_color']
    );
    
    echo $event_date;
    
    // FACEBOOK SHARE BUTTON
//    echo '<div id="fb-root"></div>
//<script>(function(d, s, id) {
//  var js, fjs = d.getElementsByTagName(s)[0];
//  if (d.getElementById(id)) return;
//  js = d.createElement(s); js.id = id;
//  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=330141470465449";
//  fjs.parentNode.insertBefore(js, fjs);
//}(document, \'script\', \'facebook-jssdk\'));</script>';
//    
//    echo '<div class="fb-share-button" data-href="' . get_permalink() . '" data-layout="button"></div>';
    
    // Purchase Tickets Placeholder
    echo '<a style="float: right;" class="button button-med" href="#"><i class="fa fa-ticket"></i>&nbsp;&nbsp;Tickets &nbsp;|&nbsp; $5</a>';
    
    
} // ucfbands_event_single_date_badge()




add_action( 'genesis_entry_header', 'ucfbands_event_single_time_location' );

/**
 * UCFBands Event - Time & Location for Single
 *
 * @author Jordan Pakrosnis
 */
function ucfbands_event_single_time_location() {
    
    $event_meta = $GLOBALS["event_meta"];

    
    // Event Location Logic
    $location = '<span class="location">';

        if ( $event_meta['location_name'] == '' )
            $location .= 'TBA';

        else
            $location .= $event_meta['location_name'];


    $location .= '</span>';
    
    
    
    // WRAPPER
    echo '<span class="event-time-location">';
    
    
        //-- EVENT TIME --//
        $event_time = ucfbands_event_time(
            $event_meta['is_all_day_event'],
            $event_meta['start_date_time'],
            $event_meta['finish_date_time'],
            $event_meta['is_time_tba'],
            $event_meta['show_finish_time']
        );

        echo $event_time;


    
        //-- EVENT LOCATION --//
        echo '<i class="fa fa-map-marker"></i> ' . $location;
    
    
    // Close Wrapper
    echo '</span>';
    
} // ucfbands_event_single_time_location() 



add_action( 'genesis_entry_header', 'ucfbands_event_single_featured_image', 15 );

/**
 * UCFBands Event - Featured Image
 *
 * @author Jordan Pakrosnis
 */
function ucfbands_event_single_featured_image() {
    
    // Check for Image
    if ( has_post_thumbnail() )
        the_post_thumbnail();
    
    else // If none, use HR
        echo '<hr class="event-header-hr">';
        
    
} // ucfbands_event_single_featured_image()



add_action( 'genesis_entry_content', 'ucfbands_event_single_content', 5 );

/**
 * UCFBands Event Single - Content with Location, Schedule, and Program Data
 *
 * @author Jordan Pakrosnis
 */
function ucfbands_event_single_content() {

    $event_meta = $GLOBALS["event_meta"];
    
    
    // CONDITIONAL LAYOUT SETUP //
    
    // Defaults
    $show_content =             true;
    $show_schedule =            true;
    $show_program =             true;
    $show_hr =                  true;
    $wrap_schedule_program =    true;
    
    // Content
    if ( get_the_content() == '' ) {
        $show_content =             false;
        $width_location =           'one-third first';
        $width_schedule =           'one-third';
        $width_program =            'one-third';
        $show_hr =                  false;
        $wrap_schedule_program =    false;
    }
    else {
        $width_content =            'two-thirds first';
        $width_location =           'one-third';
        $width_schedule =           'one-half first';
        $width_program =            'one-half';
        $show_hr =                  true;
        $wrap_schedule_program =    true;
    }
    
    // Schedule
    if ( $event_meta['schedule_group'] == null ) {
        $show_schedule = false;
        $width_program .= ' first';
    }
    
    if ( $event_meta['program_group'] == null ) {
        $show_program = false;
    }
    
    if ( ($show_schedule == false) && ($show_program == false) )
        $show_hr = false;
    
    
    
    //-- POST CONTENT --//
    if ( $show_content ) {
        echo '<div class="' . $width_content . '">';
            the_content();
        echo '</div>';
    }
    
    
    //-- LOCATION --//
    
    // Column Wrapper & Title
    echo '<div class="' . $width_location . '"><h2><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Location</h2>';
        
        // Google Map
        if ( ($event_meta['location_google_map_latitude'] != '') || ($event_meta['location_google_map_longitude'] != '') )
            ucfbands_event_google_map( $event_meta['location_google_map_latitude'], $event_meta['location_google_map_longitude'] );

        // Address
        $address = ucfbands_event_address( $event_meta['location_address'], $event_meta['location_name'] );
        echo $address;
    
    
    echo '</div>';
    
    
    // Wrap
    if ( $wrap_schedule_program )
        echo '<div class="event-schedule-repitoire clearfix">';
    
        // HR
        if ( $show_hr )
            echo '<hr>';
    
    
        // Schedule
        if ( $show_schedule ) {
            echo '<div class="' . $width_schedule . '"><h2><i class="fa fa-list"></i>&nbsp;&nbsp;Schedule</h2>';
            
                // Start UL
                echo '<ul>';
            
                    $schedule_items = $event_meta['schedule_group'];

                    //-- SCHEDULE ITEM LOOP --//
                    foreach ( $schedule_items as $schedule_item ) {

                        // Get Item Meta
                        $time =         esc_attr( $schedule_item['time'] );
                        $thing =        esc_attr( $schedule_item['thing'] );
                        $sub_items =    $schedule_item['sub_item'];
                        
                        
                        // Start List Item
                        echo '<li>';
                        
                            // Time & Thing
                            echo '<b>' . $time . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>' . $thing;
                        
                        
                            // Check for sub-items
                            if ( $sub_items != '' ) {
                                
                                // Nested UL
                                echo '<ul>';

                                    foreach ( $sub_items as $sub_item )
                                        echo '<li>' . $sub_item . '</li>';
                                
                                echo '</ul>';
                                
                            } // if sub-items
                        
                        echo '</li>';

                    } // foreach Item Loop
            
                echo '</ul>';
            
            echo '</div>';
        } // show schedule
    
    
        // Program
        if ( $show_program ) {
            echo '<div class="' . $width_schedule . '"><h2>Program</h2>';
            echo '</div>';
        } // show program
    
    
    // Wrap Close
    if ( $wrap_schedule_program )
        echo '</div>';    
    
} // ucfbands_event_single_content()



//-- LOAD FRAMEWORK --//
genesis();
