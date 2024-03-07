<?php

namespace SSA\Actions;

use SSA\Handlers\DB;
use SSA\Interfaces\Action;

class Powerpress_Action implements Action {

    static function run() {
        $audio_file_key       = DB::get_audio_file_key();
        $enclosure_only_posts = DB::get_enclosure_only_posts();

        if ( ! $enclosure_only_posts ) {
            echo '<h2>No posts for migration found</h2>';
        }

        foreach ( DB::get_enclosure_only_posts() as $enclosure ) {
            add_post_meta( $enclosure->post_id, $audio_file_key, $enclosure->enclosure );

            echo sprintf( 'Migrated post #%d', $enclosure->post_id ) . '<br>';
        }
    }

}
