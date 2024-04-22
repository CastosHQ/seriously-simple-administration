<?php
/**
 * @var $users_series_map
 * @var $add_subscribers
 * @var $revoke_subscribers
 * @var $bulk_sync_scheduled
 * @var $add_scheduled
 * @var $revoke_scheduled
 * */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<h1>Manage Memberpress</h1>

<h2>Map:</h2>
<?php echo json_encode( $users_series_map ); ?>

<h2>Subscribers To Add:</h2>
<?php echo json_encode( $add_subscribers ); ?>

<h2>Subscribers To Revoke:</h2>
<?php echo json_encode( $revoke_subscribers ); ?>

<h2>Bulk Sync Scheduled:</h2>
<?php echo $bulk_sync_scheduled ? 'true' : 'false'; ?>

<h2>Add Scheduled:</h2>
<?php echo $add_scheduled ? 'true' : 'false'; ?>

<h2>Revoke Scheduled:</h2>
<?php echo $revoke_scheduled ? 'true' : 'false'; ?>

<br><br><br>


<form method="POST" action="#">
    <input class="hidden" name="reset_memberpress_sync" value="1">
    <input class="button js-ensure" type="submit" value="RESET SUBSCRIBERS SYNC">
    <?php wp_nonce_field( 'memberpress_restart_subscribers_sync' ); ?>
</form>

<br>

<a class="button js-ensure" href="<?php echo add_query_arg('generate_csv', 'true') ?>">
    Generate CSV
</a>

<br><br>
