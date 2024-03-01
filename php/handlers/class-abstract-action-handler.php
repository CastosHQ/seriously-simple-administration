<?php

namespace SSA\Handlers;

use SSA\Entities\Enclosure;

class Abstract_Action_Handler {
    
    /**
     * @return \wpdb
     * */
    static function wpdb() {
        global $wpdb;

        return $wpdb;
    }
}
