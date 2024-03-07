<?php
/**
 * @var WP_Term[] $podcasts
 * @var int $default_id
 * */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<h1>Select the new default podcast</h1>

<form method="POST" action="#">
    <select name="new_default_series_id">
        <?php foreach ( $podcasts as $podcast ) : ?>
            <option value="<?php echo esc_attr( $podcast->term_id ) ?>" <?php selected( $podcast->term_id, $default_id ) ?>>
                <?php echo $default_id === $podcast->term_id ?
                    esc_html( ssp_get_default_series_name( $podcast->name ) ) :
                    esc_html( $podcast->name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input class="button" type="submit" value="SET AS DEFAULT">
    <?php wp_nonce_field( 'change_default_podcast_id_ ' . ssp_get_default_series_id() ); ?>
</form>
