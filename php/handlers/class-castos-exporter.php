<?php

namespace SSA\Handlers;

use SeriouslySimplePodcasting\Handlers\Castos_Handler;

class Castos_Exporter {

	private function __construct() {
	}

	/**
	 * Export missed Castos episodes containing file id
	 */
	public static function export_all_episodes_with_file_id( $schedule_mode = true ) {
		try {
			self::show_notice( 'Exporting all episodes containing Castos file id..' );
			self::check_connection();

			if ( ! class_exists( 'SeriouslySimplePodcasting\Handlers\Castos_Handler' ) ) {
				throw new \Exception( 'Error: could not find class Castos_Handler!' );
			}

			if ( $schedule_mode ) {
				$posts = self::get_episodes_with_file_id( - 1, false );

				if ( empty( $posts ) ) {
					throw new \Exception( 'No episodes for exporting found!' );
				}

				$scheduled = array();

				foreach ( $posts as $post ) {
					update_post_meta( $post->ID, 'podmotor_schedule_upload', true );
					$scheduled[] = $post->ID;
				}

				self::show_notice( sprintf( 'Scheduled exporting %d episodes: %s', count( $scheduled ), implode( ', ', $scheduled ) ) );
				self::show_notice( 'Episodes will be uploaded with cronjob within an hour. Please check the result in 1-2 hours!' );
			} else {
				$limit = filter_input( INPUT_GET, 'limit' );

				$limit = $limit ?: 100;

				$posts = self::get_episodes_with_file_id( $limit, false );

				if ( empty( $posts ) ) {
					throw new \Exception( 'No episodes for exporting found!' );
				}

				$castos_handler = new Castos_Handler();

				foreach ( $posts as $post ) {
					$response = $castos_handler->upload_episode_to_castos( $post );

					$type = ( 'success' === $response['status'] && ! empty( $response['episode_id'] ) ) ? 'success' : 'error';

					if ( 'success' === $type ) {
						update_post_meta( $post->ID, 'podmotor_episode_id', $response['episode_id'] );
					}
					self::show_notice( sprintf( 'Post %s: %s', $post->ID, $response['message'] ), $type );
				}
			}

		} catch ( \Exception $e ) {
			self::show_notice( $e->getMessage(), Notice_Handler::TYPE_WARNING );
		}
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

			$posts = self::get_episodes_with_file_id( $max );

			if ( ! class_exists( 'SeriouslySimplePodcasting\Handlers\Castos_Handler' ) ) {
				throw new \Exception( 'Error: could not find class Castos_Handler!' );
			}



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

	protected static function get_episodes_with_file_id( $max = - 1, $only_not_synced = true ) {
		$args = array(
			'post_type'      => 'podcast',
			'meta_query'     => array(
				array(
					'key'     => 'podmotor_file_id',
					'value'   => '',
					'compare' => '!=',
				),
			),
			'posts_per_page' => $max,
			'post_status'    => 'publish',
		);

		if ( $only_not_synced ) {
			$args['meta_query']['relation'] = 'AND';

			$args['meta_query'][] = array(
				array(
					'key'   => 'podmotor_episode_id',
					'value' => '',
				),
				array(
					'key'     => 'podmotor_episode_id',
					'compare' => 'NOT EXISTS',
				),
				'relation' => 'OR',
			);
		}

		$query = new \WP_Query( $args );

		return $query->get_posts();
	}

	protected static function show_notice( $notice, $type = 'success' ) {
		Notice_Handler::show_notice( $notice, $type );
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
