<?php
/**
 * Admin_Action Entity.
 * */

namespace SSA\Entities;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin_Action extends Abstract_Entity {

    /**
     * @var string
     * */
    public $title;

    /**
     * @var callable
     * */
    public $callback;
}
