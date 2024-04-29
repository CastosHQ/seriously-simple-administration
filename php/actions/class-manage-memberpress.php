<?php

namespace SSA\Actions;

use SeriouslySimplePodcasting\Integrations\Memberpress\Memberpress_Integrator;
use SSA\Handlers\Renderer;
use SSA\Interfaces\Action;

class Manage_Memberpress implements Action {

    public static function run() {
        if ( filter_input( INPUT_POST, 'reset_memberpress_sync' ) ) {
            self::reset_sync_data();

            return;
        }

        $data = array(
            'users_series_map'    => get_option( 'ss_memberpress_users_series_map' ),
            'add_subscribers'     => get_option( Memberpress_Integrator::ADD_LIST_OPTION ),
            'revoke_subscribers'  => get_option( Memberpress_Integrator::REVOKE_LIST_OPTION ),
            'bulk_sync_scheduled' => wp_next_scheduled( Memberpress_Integrator::EVENT_BULK_SYNC_SUBSCRIBERS ),
            'add_scheduled'       => wp_next_scheduled( Memberpress_Integrator::EVENT_ADD_SUBSCRIBERS ),
            'revoke_scheduled'    => wp_next_scheduled( Memberpress_Integrator::EVENT_REVOKE_SUBSCRIBERS ),
        );


        Renderer::render( 'manage-memberpress-form', $data );
    }

    protected static function reset_sync_data() {

        $events = array(
            Memberpress_Integrator::EVENT_BULK_SYNC_SUBSCRIBERS,
            Memberpress_Integrator::EVENT_ADD_SUBSCRIBERS,
            Memberpress_Integrator::EVENT_REVOKE_SUBSCRIBERS,
        );

        $i = 0;

        foreach ( $events as $event ) {
            if ( $timestamp = wp_next_scheduled( $event ) ) {
                wp_unschedule_event( $timestamp, $event );
                $i ++;
            }
        }

        printf( 'Unscheduled %d events', $i );

        echo '<br><br>';

        delete_option('ss_memberpress_users_series_map');

        echo 'Removed the map' . '<br><br>';

        delete_option(Memberpress_Integrator::ADD_LIST_OPTION);

        echo 'Removed the ADD list' . '<br><br>';

        delete_option(Memberpress_Integrator::REVOKE_LIST_OPTION);

        echo 'Removed the Revoke list' . '<br><br>';

        printf(
            'The sync data was reset! Now you can go to the <a href="%s">Integrations page</a> and click the "Save Settings" button to rerun the sync process.',
            admin_url('edit.php?post_type=podcast&page=podcast_settings&tab=integrations&integration=memberpress')
        );
    }

}
