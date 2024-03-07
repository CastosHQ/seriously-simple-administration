<?php

namespace SSA\Handlers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @author Serhiy Zakharchenko
 * @package SeriouslySimpleAdministration
 * */
class Renderer {
    /**
     * Prints the template.
     *
     * @param string $template_path
     * @param array $data
     *
     * @return void
     */
    public static function render( $template_path, $data = [] ) {
        $html = self::fetch( $template_path, $data );

        echo $html;
    }

    /**
     * Fetches the template string.s
     *
     * @param string $template_path
     * @param array $data
     *
     * @return string
     */
    public static function fetch( $template_path, $data = [] ) {
        $abs_path = '';

        // Check if there is extension in the end. If not, lets add it.
        $ext = pathinfo( $template_path, PATHINFO_EXTENSION );

        if ( ! $ext ) {
            $template_path .= '.php';
        }

        // Now try to search template in different locations
        if ( file_exists( SSA_PLUGIN_PATH . 'templates/' . $template_path ) ) {
            $abs_path = SSA_PLUGIN_PATH . 'templates/' . $template_path;
        }

        if ( ! $abs_path ) {
            return '';
        }

        extract( $data );
        ob_start();

        include $abs_path;

        $template_content = (string) ob_get_clean();

        return apply_filters( 'ssa_render_template', $template_content );
    }
}
