<?php

namespace SSA\Handlers;

use SSA\Entities\Enclosure;
use WP_Query;

class DB_Handler extends Abstract_Action_Handler {
    /**
     * @return Enclosure[]
     */
    public static function get_enclosure_only_posts() {
        $enclosure_posts      = self::get_enclosure_posts();
        $audio_file_posts     = self::get_audio_file_posts();
        $enclosure_only_posts = array();

        foreach ( $enclosure_posts as $enclosure_post ) {
            if ( ! in_array( $enclosure_post->post_id, $audio_file_posts ) ) {
                $enclosure_only_posts[] = $enclosure_post;
            }
        }

        return $enclosure_only_posts;
    }

    /**
     * @return int[]
     */
    public static function get_audio_file_posts() {
        $db = self::wpdb();

        $ssp_sql = $db->prepare( "SELECT `post_id`
                FROM {$db->postmeta} AS pm
                WHERE pm.meta_key = '%s' AND pm.meta_value != ''", self::get_audio_file_key() );

        $ssp_post_ids = $db->get_col( $ssp_sql );

        return array_values( array_map( function ( $val ) {
            return intval( $val );
        }, $ssp_post_ids ) );
    }

    public static function update_post_type( $from = 'post', $to = 'podcast', $post_ids = array() ) {
        $db = self::wpdb();

        $post_ids_str = implode( ',', array_filter( $post_ids, 'is_numeric' ) );

        $sql = $db->prepare( "UPDATE {$db->posts} AS p 
                SET post_type = %s 
                WHERE post_type = %s AND p.ID IN ($post_ids_str)", $to, $from );

        $res = $db->query( $sql );

        if( $res ){
            echo sprintf( '<h2>Updated %d posts</h2>', $res );
        } else {
            echo '<h2>Nothing to update</h2>';
        }
    }

    public static function get_audio_file_key() {
        return apply_filters( 'ssp_audio_file_meta_key', 'audio_file' );
    }

    /**
     * @return Enclosure[]
     */
    public static function get_enclosure_posts() {
        $db     = self::wpdb();
        $pp_sql = "SELECT `post_id`, pm.`meta_value`
                FROM $db->postmeta AS pm
                WHERE pm.meta_key = 'enclosure' AND pm.meta_value != ''";

        $enclosures = $db->get_results( $pp_sql );
        foreach ( $enclosures as $k => $enclosure ) {
            $enclosures[ $k ] = new Enclosure( $enclosure );
        }

        return $enclosures;
    }
}
