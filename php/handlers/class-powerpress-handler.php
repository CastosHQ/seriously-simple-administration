<?php

namespace SSA\Handlers;

class Powerpress_Handler extends Abstract_Action_Handler {

    static function migrate() {
        $audio_file_key       = DB_Handler::get_audio_file_key();
        $enclosure_only_posts = DB_Handler::get_enclosure_only_posts();

        if ( ! $enclosure_only_posts ) {
            echo '<h2>No posts for migration found</h2>';
        }

        foreach ( DB_Handler::get_enclosure_only_posts() as $enclosure ) {
            add_post_meta( $enclosure->post_id, $audio_file_key, $enclosure->enclosure );

            echo sprintf( 'Migrated post #%d', $enclosure->post_id ) . '<br>';
        }
    }

}
