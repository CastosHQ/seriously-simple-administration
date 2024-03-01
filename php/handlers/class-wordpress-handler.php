<?php

namespace SSA\Handlers;

class Wordpress_Handler extends Abstract_Action_Handler {
    static function migrate_posts_to_episodes() {
        $post_ids = DB_Handler::get_audio_file_posts();

        DB_Handler::update_post_type('post', SSP_CPT_PODCAST, $post_ids );
    }
}
