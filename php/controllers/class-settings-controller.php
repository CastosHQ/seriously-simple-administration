<?php

namespace SSA\Controllers;


use SSA\Entities\Admin_Action;
use SSA\Handlers\Notice_Handler;
use SSA\Handlers\Powerpress_Handler;
use SSA\Handlers\SSP_Admin;

class Settings_Controller extends Abstract_Controller {

    /**
     * Constructor function.
     * @since   1.4.0
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
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
        echo '<h1>Admin settings</h1>';

        if ( isset( $_GET['ssa_admin_action'] ) ) {
            $admin_action = filter_var( $_GET['ssa_admin_action'] );
            $admin_actions = $this->get_admin_actions();

            if ( isset( $admin_actions[ $admin_action ] ) ) {
                call_user_func( $admin_actions[ $admin_action ]->callback );
            }

            echo '<p><a class="button" href="' . remove_query_arg( 'ssa_admin_action' ) . '"><< Go back</a></p>';

        } else {
            $this->print_action_buttons();
        }

        echo '</div>';
    }

    protected function print_action_buttons() {
        $log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
        $log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
        if ( is_file( $log_path ) ) {
            echo '<p><a class="button" href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
        }

        foreach ( $this->get_admin_actions() as $action_key => $action ) {
            $action_url = add_query_arg( 'ssa_admin_action', $action_key );
            echo '<p><a class="button" href="' . esc_url( $action_url ) . '">' . $action->title . '</a></p>';
        }

        $this->activate_action_warning();
    }

    protected function activate_action_warning() {
        ?>
        <script>
			  jQuery( document ).ready( function( $ ) {
				  $( '.ssa-settings' ).find( '.button' ).click( function( e ) {
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
     * @return Admin_Action[]
     */
    public function get_admin_actions() {
        return array(
            'migrate_powerpress' => new Admin_Action( array(
                'title'    => 'Migrate Powerpress',
                'callback' => array( Powerpress_Handler::class, 'migrate' ),
            ) ),
        );
    }
}
