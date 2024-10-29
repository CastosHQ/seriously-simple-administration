<?php

namespace SSA\Actions;

use SSA\Handlers\DB;

class Migrate_From_Posts_Action extends Abstract_Action {

    protected $id = 'migrate_from_posts';

    protected $title = 'Migrate From Posts';

    protected $description = 'Finds posts with the `audio_file` meta field and changes their post type to podcast.';


    public function run() {
        $post_ids = DB::get_audio_file_posts();

        DB::update_post_type( 'post', SSP_CPT_PODCAST, $post_ids );
    }
}
