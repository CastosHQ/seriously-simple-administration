<?php

namespace SSA\Controllers;


use SSA\Actions\Abstract_Action;
use SSA\Actions\Manage_Memberpress;
use SSA\Actions\Manage_Memberpress_Action;
use SSA\Entities\Admin_Action;
use SSA\Handlers\Notice_Handler;

class Settings_Controller extends Abstract_Controller {

    /**
     * Constructor function.
     * @since   1.4.0
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

        add_action( 'admin_init', array( $this, 'generate_memberpress_csv' ) );
    }

    public function generate_memberpress_csv() {
        if (
            current_user_can( 'manage_options' ) &&
            'manage_memberpress' === filter_input( INPUT_GET, 'ssa_admin_action' ) &&
            'true' === filter_input( INPUT_GET, 'generate_csv' )
        ) {
            Manage_Memberpress_Action::generate_csv();
        }
    }

    public function add_menu_item() {
        add_submenu_page(
            'edit.php?post_type=podcast',
            __( 'Administration', 'seriously-simple-podcasting' ),
            __( 'Administration', 'seriously-simple-podcasting' ),
            'manage_podcast',
            'admin',
            array( $this, 'setup_development_settings' )
        );
    }

    public function setup_development_settings() {

        echo '<div class="wrap ssa-settings">';
        echo '<h1>Admin Actions</h1>';

        if ( isset( $_GET['ssa_admin_action'] ) ) {
            $action_string  = filter_var( $_GET['ssa_admin_action'], FILTER_SANITIZE_STRING );
            $admin_actions = $this->get_admin_actions();

            foreach ( $admin_actions as $admin_action ) {
                if( $admin_action->id() == $action_string ){
                    $admin_action->run();
                    break;
                }
            }

            echo '<p><a class="button" href="' . remove_query_arg( 'ssa_admin_action' ) . '"><< Go back</a></p>';

        } else {
            $this->render_admin_actions();
        }

        echo '</div>';

        $this->activate_action_warning();
    }

    protected function render_admin_actions() {
        $log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
        $log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
        if ( is_file( $log_path ) ) {
            echo '<p><a class="button" href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
        }

        foreach ( $this->get_admin_actions() as $action ) {
            $action_url = add_query_arg( 'ssa_admin_action', $action->id() );
            $action_url = add_query_arg( 'nonce', wp_create_nonce( $action->id() ), $action_url );
            echo '<h2>' . esc_html( $action->title() ) . '</h2>';
            echo '<p>' . esc_html( $action->description() ) . '</p>';
            echo '<p><a class="button js-ensure" href="' . esc_url( $action_url ) . '">' . esc_html( $action->title() ) . '</a></p><br>';
        }
    }

    protected function activate_action_warning() {
        ?>
        <script>
			  jQuery( document ).ready( function( $ ) {
				  $( '.ssa-settings' ).find( '.js-ensure' ).click( function( e ) {
					  if ( ! confirm( 'Are you sure?' ) ) {
						  e.preventDefault();
						  e.stopPropagation();
					  }
				  } );
			  } );
        </script>
        <?php
    }

    /**
     * @param string $notice
     * @param string $type
     * @param bool $is_dismissible
     *
     * @return void
     */
    public function show_notice( $notice, $type = 'success', $is_dismissible = true ) {
        Notice_Handler::show_notice( $notice, $type, $is_dismissible );
    }

    /**
     * @return Abstract_Action[]
     */
    public function get_admin_actions() {
        $actions = require SSA_PLUGIN_PATH . 'php/config/admin-actions.php';

        return apply_filters( 'ssa_actions', $actions );
    }
}
