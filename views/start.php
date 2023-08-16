<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>
<div id="fdestadisticas-plugin-container">

	<div class="fdestadisticas-lower">

		<div class="fdestadisticas-boxes">
                  
                  <div class="wrap">

                    <h2><?php _e('Finanzas Digital - API (Shortcode)') ?></h2>

                    <form class="ds8-form" method="post" action="options.php">
                    <?php settings_fields('ds8-settings-group'); ?>
                    <?php do_settings_sections('ds8-settings-page') ?>

                        <table class="form-table">
                        <?php DS8LastestPostsAdmin::create_form($options); ?>
                          <tr valign="top">
                            <th scope="row">API JSON</th>
                            <td> <?php  echo '/wp-json/wp/v2/' ?></td>
                          </tr>
                        </table>

                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>

                    </form>
                  </div>
                  
		</div>
	</div>
</div>