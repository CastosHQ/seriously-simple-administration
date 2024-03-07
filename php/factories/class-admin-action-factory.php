<?php

namespace SSA\Factories;

use SSA\Entities\Admin_Action;

class Admin_Action_Factory {
    /**
     * @param string $title
     * @param callable $action
     *
     * @return Admin_Action
     */
    public static function build( $title, $action ){
        return new Admin_Action( array(
            'title'    => $title,
            'callback' => $action,
        )  );
    }
}
