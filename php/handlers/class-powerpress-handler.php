<?php

namespace SSA\Handlers;

use SSA\Entities\Enclosure;

class Powerpress_Handler {

    static function migrate() {
        $audio_file_key       = self::get_audio_file_key();
        $enclosure_only_posts = self::get_enclosure_only_posts();

        if ( ! $enclosure_only_posts ) {
            echo 'No posts for migration found';
        }

        foreach ( self::get_enclosure_only_posts() as $enclosure ) {
            add_post_meta( $enclosure->post_id, $audio_file_key, $enclosure->enclosure );

            echo sprintf( 'Migrated post #%d', $enclosure->post_id ) . '<br>';
        }
    }

    /**
     * @return Enclosure[]
     */
    protected static function get_enclosure_only_posts() {
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
    protected static function get_audio_file_posts() {
        $db = self::wpdb();

        $ssp_sql = $db->prepare( "SELECT `post_id`
                FROM {$db->postmeta} AS pm
                WHERE pm.meta_key = '%s' AND pm.meta_value != ''", self::get_audio_file_key() );

        $ssp_post_ids = $db->get_col( $ssp_sql );

        return array_values( array_map( function ( $val ) {
            return intval( $val );
        }, $ssp_post_ids ) );
    }

    protected static function get_audio_file_key() {
        return apply_filters( 'ssp_audio_file_meta_key', 'audio_file' );
    }

    /**
     * @return Enclosure[]
     */
    protected static function get_enclosure_posts() {
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

    /**
     * @return \wpdb
     * */
    static function wpdb() {
        global $wpdb;

        return $wpdb;
    }

}
