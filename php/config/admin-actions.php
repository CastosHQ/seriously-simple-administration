<?php

use SSA\Actions\Change_Default_Podcast_Action;
use SSA\Actions\Manage_Memberpress;
use SSA\Actions\Migrate_From_Posts_Action;
use SSA\Actions\Powerpress_Action;
use SSA\Factories\Admin_Action_Factory;

return array(
    'migrate_powerpress'     => Admin_Action_Factory::build(
        'Migrate Powerpress', array( Powerpress_Action::class, 'run' )
    ),
    'migrate_from_posts'     => Admin_Action_Factory::build(
        'Migrate From Posts', array( Migrate_From_Posts_Action::class, 'run' )
    ),
    'change_default_podcast' => Admin_Action_Factory::build(
        'Change Default Podcast', array( Change_Default_Podcast_Action::class, 'run' )
    ),
    'manage_memberpress' => Admin_Action_Factory::build(
        'Manage Memberpress Sync', array( Manage_Memberpress::class, 'run' )
    ),
);
