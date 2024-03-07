<?php

namespace SSA\Actions;

use SSA\Handlers\DB;
use SSA\Interfaces\Action;

class Migrate_From_Posts_Action implements Action {
    public static function run() {
        $post_ids = DB::get_audio_file_posts();

        DB::update_post_type('post', SSP_CPT_PODCAST, $post_ids );
    }
}
