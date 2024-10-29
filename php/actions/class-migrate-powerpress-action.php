<?php

namespace SSA\Actions;

use SSA\Entities\Enclosure;
use SSA\Handlers\DB;

class Migrate_Powerpress_Action extends Abstract_Action {

    protected $id = 'migrate_powerpress';

    protected $title = 'Migrate Powerpress';

    protected $description = 'Finds episodes with an `enclosure` meta field but an empty `audio_file` meta field, retrieves the file URL from `enclosure`, and sets it to `audio_file`.';

    public function run() {
        $enclosure_only_posts = DB::get_enclosure_only_posts();

        $this->copy_enclosure_data( $enclosure_only_posts );
    }

    /**
     * @param Enclosure[] $enclosures
     *
     * @return void
     */
    public function copy_enclosure_data( $enclosures ) {
        $audio_file_key = DB::get_audio_file_key();

        if ( ! $enclosures ) {
            echo '<h2>No posts for migration found</h2>';
        }

        foreach ( $enclosures as $enclosure ) {

            if ( ! $enclosure->url() ) {
                echo sprintf( 'Empty enclosure for post #%d, skipped', $enclosure->post_id ) . '<br>';
                continue;
            }

            update_post_meta( $enclosure->post_id, $audio_file_key, $enclosure->url() );

            if ( $enclosure->duration ) {
                update_post_meta( $enclosure->post_id, 'duration', $enclosure->duration );
            }

            if ( $enclosure->explicit ) {
                update_post_meta( $enclosure->post_id, 'explicit', 'on' );
            }

            if ( $filesize = $enclosure->file_size_raw() ) {
                update_post_meta( $enclosure->post_id, 'filesize_raw', $filesize );
                update_post_meta( $enclosure->post_id, 'filesize', $enclosure->file_size_formatted() );
            }

            echo sprintf( 'Migrated post #%d', $enclosure->post_id ) . '<br>';
        }
    }

}
