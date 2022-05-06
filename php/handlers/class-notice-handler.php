<?php

namespace SSA\Handlers;

/**
 * SSP Controllers Handler.
 * Stores all the controllers in one place.
 */
class Notice_Handler {

	const TYPE_INFO = 'info';
	const TYPE_SUCCESS = 'success';
	const TYPE_ERROR = 'error';
	const TYPE_WARNING = 'warning';

	/**
	 * @param string $notice
	 * @param string $type
	 * @param bool $is_dismissible
	 *
	 * @return void
	 */
	public static function show_notice( $notice, $type = 'success', $is_dismissible = true ) {
		$type = in_array( $type, self::get_valid_types() ) ? $type : 'success';
		?>
        <div class="notice notice-<?php echo $type; ?><?php if ( $is_dismissible ): ?> is-dismissible<?php endif; ?>">
            <p><?php _e( $notice ); ?></p>
        </div>
		<?php
	}

	protected static function get_valid_types() {
		return array(
			self::TYPE_WARNING,
			self::TYPE_INFO,
			self::TYPE_ERROR,
			self::TYPE_SUCCESS
		);
	}
}
