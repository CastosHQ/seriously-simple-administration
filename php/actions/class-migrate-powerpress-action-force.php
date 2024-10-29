<?php

namespace SSA\Actions;

use SSA\Handlers\DB;

class Migrate_Powerpress_Action_Force extends Migrate_Powerpress_Action {

    protected $id = 'migrate_powerpress_force';

    protected $title = 'Migrate Powerpress (Force)';

    protected $description = 'Finds all episodes with an `enclosure` meta field, retrieves the file URL from `enclosure`, and sets it to `audio_file`.';

    public function run() {
        $enclosures = DB::get_enclosure_posts();

        $this->copy_enclosure_data( $enclosures );
    }
}
