<?php if ( ! defined( 'ABSPATH' ) ) die(); ?>

<?php
if ( isset( $_POST['yandex-metrica-authorize'] ) ) {
	$this->options['authcode'] = intval( $_POST['auth-code'] );

	if ( $this->authorize( esc_attr( $this->options['authcode'] ) ) ) {
		echo '<div class="updated"><p>' . __( 'Successfully connected to Yandex Server', 'yandex_metrica' ) . '</p></div>';
	}
	else {
		echo '<div class="error"><p>' . __( 'Something went wrong. Please check your confirmation code!', 'yandex_metrica' ) . '</p></div>';
	}
}

if ( isset( $_POST["backward"] ) ) {
	$this->options['backward'] = true; // enable backward compatibility
	$this->update_options( $this->options );
}

if ( isset( $_POST["yandex-metrica-save"] ) ) {
	$this->options["counter_id"]     = intval( $_POST["metrica-counter"] );
	$this->options['webvisor']       = empty( $_POST['metrica_webvisor'] ) ? false : true;
	$this->options['clickmap']       = empty ( $_POST['metrica_clickmap'] ) ? false : true;
	$this->options['tracklinks']     = empty( $_POST['metrica_tracklinks'] ) ? false : true;
	$this->options['accurate_track'] = empty( $_POST['metrica_accurate_track'] ) ? false : true;
	$this->options['track_hash']     = empty( $_POST['track_hash'] ) ? false : true;

	$this->options['track-logged-in']     = ( $_POST['track-logged-in'] == "no" ) ? false : true;
	$this->options["untrack-roles"]       = ! empty( $_POST["tracker_role"] ) ? array_map( 'esc_attr', $_POST["tracker_role"] ) : "";
	$this->options["widget-access-roles"] = ! empty( $_POST["widget_access"] ) ? array_map( 'esc_attr', $_POST["widget_access"] ) : "";


	if ( is_numeric( $_POST["metrica-counter"] ) ) {
		echo '<div class="updated"><p>' . __( 'Options Saved!', 'yandex_metrica' ) . '</p></div>';
	}
	else {
		echo '<div class="error fade"><p>' . __( "Please enter a valid counter code!", "yandex_metrica" ) . '</a></p></div>';
		$this->options["counter_id"] = null;
	}

	$this->update_options( $this->options );

}


if ( isset( $_POST["reset"] ) ) {
	$this->update_options( null );
	$this->options = $this->get_options(); // call default options

	echo ' <div class="updated"><p>' . __( 'All options cleared!', 'yandex_metrica' ) . '</p></div>';
}

?>



<div class="wrap">
	<form method="post" action="">

		<h2><?php _e( 'Yandex Metrica', 'yandex_metrica' ); ?></h2>

		<?php if ( ! $this->is_authorized() && $this->options["backward"] === false ) : ?>
			<p><?php _e( 'You need sign in to Yandex and grant this plugin access to your Yandex Metrica account.', 'yandex_metrica' ); ?></p>
			<p class="button" onclick="window.open('<?php printf('%sauthorize?response_type=code&client_id=%s&display=popup',__('https://oauth.yandex.com/','yandex_metrica'), self::YANDEX_APP_ID); ?>', 'activate','width=600, height=500, menubar=0, status=0, location=0, toolbar=0')">
				<a style="text-decoration: none;" target="_blank" href="javascript:void(0);"><b><?php _e( 'Click here to getting confirmation code', 'yandex_metrica' ); ?></b></a>
			</p>

			<div id="metrica-settings">
				<input type="text" name="auth-code" placeholder="<?php _e( 'Enter Confirmation code here', 'yandex_metrica' ); ?>" style="width: 300px;" />
				<?php submit_button( __( 'Save', 'yandex_metrica' ), 'primary', 'yandex-metrica-authorize', false ); ?>
			</div>

			<hr>
			<p><?php _e( "If you don't want to use API, you can use basic mode.", "yandex_metrica" ); ?></p>
			<?php submit_button( __( 'Basic Mode (Compatible with Backward)', 'yandex_metrica' ), 'button', 'backward', false ); ?>


		<?php else: ?>
			<?php if ( $this->options["backward"] === false ): ?>
				<?php if ( ! is_null( self::$metrica_api->get_counters() ) ): ?>
					<label for="metrica-counter"><?php _e( 'Counter:', 'yandex_metrica' ); ?></label>
					<select name="metrica-counter" id="metrica-counter">
						<?php foreach ( self::$metrica_api->get_counters() as $counter ): ?>
							<option <?php if ( ! empty( $this->options["counter_id"] ) ) selected( $this->options["counter_id"], $counter['id'] ); ?>   value="<?php echo $counter['id']; ?>"><?php echo $counter['site']; ?></option>
						<?php endforeach; ?>
					</select>
				<?php else:
					echo '<div class="error"><p>' . __( 'Temporary, getting connectivity problem.!', 'yandex_metrica' ) . '</p></div>';
				?>
					<br />
				<?php endif; ?>
			<?php else: ?>
				<input type="text" name="metrica-counter" <?php if ( isset( $this->options["counter_id"] ) ) echo 'value="' . esc_attr( $this->options["counter_id"] ) . '"'; ?> placeholder="<?php _e( 'Enter counter number', 'yandex_metrica' ); ?>" style="width:300px;" metrica-counter" />
        <?php endif; ?>

			<h3><?php _e( 'Tracking Settings', 'yandex_metrica' ); ?></h3>

			<table class="form-table">

				<tbody>

				<tr valign="top">
					<th>
						<label><?php _e( 'Select tracking options', 'yandex_metrica' ); ?></label>
					</th>
					<td>
						<label><input type="checkbox" <?php checked( $this->options['webvisor'] ); ?>     name="metrica_webvisor" value="1">  <?php _e( 'Webvisor', 'yandex_metrica' ) ?>
						</label><br>
						<label><input type="checkbox" <?php checked( $this->options['clickmap'] ); ?>     name="metrica_clickmap" value="1">  <?php _e( 'Track Clickmap', 'yandex_metrica' ); ?>
						</label><br>
						<label><input type="checkbox" <?php checked( $this->options['tracklinks'] ); ?>   name="metrica_tracklinks" value="1">  <?php _e( 'Track Links, social sharing, file requests...', 'yandex_metrica' ); ?>
						</label><br>
						<label><input type="checkbox" <?php checked( $this->options['accurate_track'] ); ?>   name="metrica_accurate_track" value="1">  <?php _e( 'Accurate Track Bounce', 'yandex_metrica' ); ?>
						</label><br>
						<label><input type="checkbox" <?php checked( $this->options['track_hash'] ); ?>   name="track_hash" value="1">  <?php _e( "Hash tracking in the browser's address bar", 'yandex_metrica' ); ?>
						</label><br>
					</td>
				</tr>

				<tr valign="top">
					<th>
						<label><?php _e( 'Track logged in users', 'yandex_metrica' ); ?></label>
					</th>
					<td>
						<select name="track-logged-in">
							<option <?php selected( $this->options["track-logged-in"] ); ?> value="yes"><?php _e( 'Yes', 'yandex_metrica' ); ?></option>
							<option <?php selected( $this->options["track-logged-in"], false ); ?> value="no"><?php _e( 'No', 'yandex_metrica' ); ?></option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th>
						<label><?php _e( 'User roles to not track', 'yandex_metrica' ); ?></label>
					</th>
					<td>
						<?php
						$roles = $wp_roles->get_names();
						$untrack_roles = $this->options["untrack-roles"]; // get roles that not tracking if logged users track tuned on
						if ( ! is_array( $untrack_roles ) ) $untrack_roles = array();

						foreach ( $roles as $role => $name ): ?>
							<input type="checkbox" <?php if ( in_array( $role, $untrack_roles ) ) echo "checked"; ?>  name="tracker_role[]" value="<?php echo $role; ?>" /> <?php echo translate_user_role( $name ); ?>
							<br />
						<?php endforeach; ?>

						<p class="setting-description"><?php _e( "If a user is logged into with one of these roles, they won't track by metrica.", 'yandex_metrica' ); ?></p>
					</td>
				</tr>
				<?php if ( $this->options["backward"] === false ): ?>
					<tr valign="top">
						<th>
							<label><?php _e( 'User roles to display dashboard widget', 'yandex_metrica' ); ?></label>
						</th>
						<td>
							<?php
							$widget_roles = $this->options["widget-access-roles"]; // get roles that not tracking if logged users track tuned on
							if ( ! is_array( $widget_roles ) ) $widget_roles = array();

							foreach ( $roles AS $role => $name ): ?>

								<input type="checkbox" <?php if ( in_array( $role, $widget_roles ) ) echo "checked"; ?>  name="widget_access[]" value="<?php echo $role; ?>" /> <?php echo translate_user_role( $name ); ?>
								<br />

							<?php endforeach; ?>

							<p class="setting-description"><?php _e( 'Selected roles can display metrica statistic on the dashboard.', 'yandex_metrica' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>

			<div class="save">
				<?php submit_button( __( 'Save', 'yandex_metrica' ), 'primary', 'yandex-metrica-save', false ); ?>
				<input type="submit" name="reset" value="<?php echo __( 'Reset', 'yandex_metrica' ); ?>" class="button-secondary" />
			</div>

		<?php endif; ?>
	</form>
</div>