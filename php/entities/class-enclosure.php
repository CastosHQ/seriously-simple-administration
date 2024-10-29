<?php
/**
 * Enclosure Entity.
 * */

namespace SSA\Entities;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @property $duration
 * @property $explicit
 * */
class Enclosure extends Abstract_Entity {

    /**
     * @var string
     * */
    public $post_id;

    /**
     * @var string
     * */
    public $enclosure;

    /**
     * @var array $data
     * */
    protected $data;

    /**
     * @var array $file_size
     * */
    protected $file_size;

    public function __construct( $properties ) {
        parent::__construct( $properties );

        if ( isset( $properties->meta_value ) ) {
            $this->enclosure = $properties->meta_value;
        }
    }

    public function __get( $prop ) {
        return isset( $this->data()[ $prop ] ) ? $this->data()[ $prop ] : '';
    }

    public function file_size_raw() {
        if ( is_null( $this->file_size ) ) {
            $this->file_size = '';
            $parts           = explode( "\n", $this->enclosure );
            if ( is_array( $parts ) && count( $parts ) > 1 ) {
                $this->file_size = $parts[1];
            }
        }

        return intval( $this->file_size );
    }

    public function file_size_formatted( $precision = 2 ) {
        if ( ! $this->file_size_raw() ) return false;

        $base           = log( $this->file_size_raw() ) / log( 1024 );
        $suffixes       = array( '', 'k', 'M', 'G', 'T' );
        $formatted_size = round( pow( 1024, $base - floor( $base ) ), $precision ) . $suffixes[ floor( $base ) ];

        return apply_filters( 'ssp_file_size_formatted', $formatted_size, $this->file_size_raw() );
    }

    public function data() {
        if ( is_null( $this->data ) ) {
            $this->data = array();
            $parts      = explode( "\n", $this->enclosure );
            if ( is_array( $parts ) && count( $parts ) > 3 && is_serialized( $parts[3] ) ) {
                $this->data = unserialize( $parts[3] );
            }
        }

        return $this->data;
    }

    public function url() {

        $enclosure = $this->enclosure;

        // If the enclosure is empty, return null
        if ( empty( $enclosure ) ) {
            return null;
        }

        // Case 1: Array
        if ( is_array( $enclosure ) && isset( $enclosure['url'] ) ) {
            return $enclosure['url'];
        }

        // Case 2: Serialized array
        if ( is_serialized( $enclosure ) ) {
            $data = unserialize( $enclosure );
            if ( isset( $data['url'] ) ) {
                return $data['url'];
            }
        }

        // Case 3: String with additional info (URL size type) or just a URL string
        // Split by spaces
        $parts = explode( "\n", $enclosure );
        if ( filter_var( $parts[0], FILTER_VALIDATE_URL ) ) {
            return $parts[0];
        }

        // If none of the above cases match, return null
        return null;
    }
}
