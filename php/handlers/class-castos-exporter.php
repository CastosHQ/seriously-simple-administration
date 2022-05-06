<?php

namespace SSA\Handlers;

use SeriouslySimplePodcasting\Handlers\Castos_Handler;

class Castos_Exporter {

	private function __construct() {
	}

	/**
	 * Export missed Castos episodes containing file id
	 *
	 * @param int $max
	 */
	public static function export_missed_episodes_with_file_id( $max = 10 ) {
		try {
			self::show_notice( 'Exporting missed episodes containing file id' );
			self::check_connection();

			$posts = self::get_podcasts_with_file_id( $max );

			if ( ! class_exists( 'SeriouslySimplePodcasting\Handlers\Castos_Handler' ) ) {
				throw new \Exception( 'Error: could not find class Castos_Handler!' );
			}

			$castos_handler = new Castos_Handler();

			if ( empty( $posts ) ) {
				self::show_notice( 'No episodes for exporting found!' );
			}

			foreach ( $posts as $post ) {
				$response = $castos_handler->upload_episode_to_castos( $post );

				$type = ( 'success' === $response['status'] && ! empty( $response['episode_id'] ) ) ? 'success' : 'error';

				if ( 'success' === $type ) {
					update_post_meta( $post->ID, 'podmotor_episode_id', $response['episode_id'] );
				}
				self::show_notice( sprintf( 'Post %s: %s', $post->ID, $response['message'] ), $type );
			}

		} catch ( \Exception $e ) {
			self::show_notice( $e->getMessage(), 'error' );
		}
	}

	protected static function get_podcasts_with_file_id( $max ) {
		$args = array(
			'post_type'      => 'podcast',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'podmotor_file_id',
					'value'   => '',
					'compare' => '!=',
				),
				array(
					array(
						'key'   => 'podmotor_episode_id',
						'value' => '',
					),
					array(
						'key'     => 'podmotor_episode_id',
						'compare' => 'NOT EXISTS',
					),
					'relation' => 'OR',
				),
			),
			'posts_per_page' => $max,
			'post_status'    => 'publish',
		);

		$query = new \WP_Query( $args );

		return $query->get_posts();
	}

	protected static function show_notice( $notice, $type = 'success' ) {
		$type = in_array( $type, array( 'success', 'error' ) ) ? $type : 'success';
		?>
        <div class="notice notice-<?php echo $type; ?> is-dismissible">
            <p><?php _e( $notice ); ?></p>
        </div>
		<?php
	}

	protected static function check_connection() {
		if ( ! function_exists( 'ssp_is_connected_to_castos' ) || ! ssp_is_connected_to_castos() ) {
			throw new \Exception( 'Error: could not connect to Castos!' );
		}

		if ( ! class_exists( 'SeriouslySimplePodcasting\Handlers\Castos_Handler' ) ) {
			throw new \Exception( 'Error: could not find class Castos_Handler!' );
		}
	}

	/**
	 * Todo
	 * */
	public static function export_episodes_without_file_id() {
	}

	/**
	 * Todo
	 * */
	protected static function export_post_file( $post ) {
	}
}
