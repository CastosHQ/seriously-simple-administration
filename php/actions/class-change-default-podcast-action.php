<?php

namespace SSA\Actions;

use SSA\Handlers\Renderer;
use SSA\Interfaces\Action;

class Change_Default_Podcast_Action implements Action {

    public static function run() {
        $id = filter_input( INPUT_POST, 'new_default_series_id', FILTER_VALIDATE_INT );
        if ( $id && check_admin_referer( 'change_default_podcast_id_ ' . ssp_get_default_series_id() ) ) {
            self::change_series_id( $id );

            return;
        }
        $podcasts   = ssp_get_podcasts();
        $default_id = ssp_get_default_series_id();
        Renderer::render( 'change-default-podcast-form', compact( 'podcasts', 'default_id' ) );
    }

    protected static function change_series_id( $id ) {
        $current_series_id = ssp_get_default_series_id();
        if ( is_numeric( $id ) && $id != $current_series_id ) {
            $podcast = get_term_by( 'id', $id, ssp_series_taxonomy() );
            if ( $podcast ) {
                ssp_update_option( 'default_series', $id );
                echo 'Default podcast was successfully changed to ' . esc_html( $podcast->name );

                return;
            }
        }

        echo 'Default podcast was not changed!';
    }

}
