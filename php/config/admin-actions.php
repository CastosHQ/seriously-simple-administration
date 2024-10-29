<?php

use SSA\Actions\Change_Default_Podcast_Action;
use SSA\Actions\Manage_Memberpress_Action;
use SSA\Actions\Migrate_From_Posts_Action;
use SSA\Actions\Migrate_Powerpress_Action;
use SSA\Actions\Migrate_Powerpress_Action_Force;

return array(
    new Migrate_Powerpress_Action(),
    new Migrate_Powerpress_Action_Force(),
    new Migrate_From_Posts_Action(),
    new Change_Default_Podcast_Action(),
    new Manage_Memberpress_Action(),
);
