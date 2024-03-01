<?php
/**
 * Enclosure Entity.
 * */

namespace SSA\Entities;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Enclosure extends Abstract_Entity {

    /**
     * @var string
     * */
    public $post_id;

    /**
     * @var string
     * */
    public $enclosure;

    public function __construct( $properties ) {
        parent::__construct( $properties );

        if ( isset( $properties->meta_value ) ) {
            $this->enclosure = $properties->meta_value;
        }
    }
}
