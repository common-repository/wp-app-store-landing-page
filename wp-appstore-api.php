<?php
/**
 * Plugin Name: WP App Store API
 * Plugin URI: http://lioncitylab.com/wordpress
 * Description: The WP App Store API allows you to search the App Store for any app information.
 * Version: 1.0.3
 * Author: Sherizan
 * Author URI: http://lioncitylab.com/
 * License: GPL2
 */

// Register Media Upload + JS
add_action('admin_enqueue_scripts', 'wp_appstore_api_scripts');
 
function wp_appstore_api_scripts() {
  if (isset($_GET['page']) && $_GET['page'] == 'wp-appstore-api-settings') {
    wp_enqueue_media();
    wp_register_script('wp-appstore-api-js', plugins_url('wp-appstore-api.js', __FILE__ , array('jquery')));
    wp_enqueue_script('wp-appstore-api-js');
  }
}

// Register style sheet
add_action( 'admin_enqueue_scripts', 'wp_appstore_api_styles' );

function wp_appstore_api_styles() {
  if (isset($_GET['page']) && $_GET['page'] == 'wp-appstore-api-settings') {
    wp_register_style( 'wp-appstore-api-css', plugins_url('wp-appstore-api.css', __FILE__ ) );
    wp_enqueue_style( 'wp-appstore-api-css' );
  }
}

add_action('admin_menu', 'wp_appstore_api_menu');
add_action( 'admin_init', 'wp_appstore_api_settings' );

function wp_appstore_api_menu() {
  add_menu_page('App Store API Settings', 'App Store API', 'administrator', 'wp-appstore-api-settings', 'wp_appstore_api_settings_page', 'dashicons-schedule');
}

function wp_appstore_api_settings() {

  // App Details
  register_setting( 'wp-appstore-api-settings-group', 'itunes_id' );
  register_setting( 'wp-appstore-api-settings-group', 'app_url' );
  register_setting( 'wp-appstore-api-settings-group', 'app_android_url' );
  register_setting( 'wp-appstore-api-settings-group', 'app_icon' );
  register_setting( 'wp-appstore-api-settings-group', 'app_name' );
  register_setting( 'wp-appstore-api-settings-group', 'app_tagline' );
  register_setting( 'wp-appstore-api-settings-group', 'app_description' );
  register_setting( 'wp-appstore-api-settings-group', 'app_price' );
  register_setting( 'wp-appstore-api-settings-group', 'app_currency' );

  // App Visual
  register_setting( 'wp-appstore-api-settings-group', 'app_upload_01' );
  register_setting( 'wp-appstore-api-settings-group', 'app_background_color' );
  register_setting( 'wp-appstore-api-settings-group', 'app_primary_color' );
  register_setting( 'wp-appstore-api-settings-group', 'app_font_color' );
  register_setting( 'wp-appstore-api-settings-group', 'app_mobile_device' );

  // Social Media
  register_setting( 'wp-appstore-api-settings-group', 'app_facebook_url' );
  register_setting( 'wp-appstore-api-settings-group', 'app_twitter_url' );
  register_setting( 'wp-appstore-api-settings-group', 'app_instagram_url' );

}

function wp_appstore_api_settings_page() {

?>

<div class="wrap" style="width:65%;float:left;padding:10px;">

  <form method="post" action="options.php">

    <?php settings_fields( 'wp-appstore-api-settings-group' ); ?>
    <?php do_settings_sections( 'wp-appstore-api-settings-group' ); ?>
    
    <div class="admin-block">

      <h3>App Store ID</h3>

      <hr>

      <table class="form-table">
        <tr valign="top">
          <th scope="row">Enter your App Store ID<br><small>[appstore type="id"]</small></th>
          <td><input type="text" name="itunes_id" value="<?php echo esc_attr( get_option('itunes_id') ); ?>" /></td>
        </tr>
      </table>

      <p>If your App store link is https://itunes.apple.com/app/id<b>626207936</b>, the ID is <b>626207936</b></p>
      
      <?php 

        $app_id = esc_attr( get_option('itunes_id') );

        if ($app_id != "" ) {

          $json = @file_get_contents("https://itunes.apple.com/lookup?id=$app_id");

          if ($json) {

            $data = json_decode($json,true);

            if ($data['resultCount'] === 0 || $data['results'][0]['wrapperType'] === "artist" || $data['results'][0]['wrapperType'] === "collection") {

              echo "Whoops! The app you are looking for is actually an artist or an album.";

            } else {

              $app_url = $data['results'][0]['trackViewUrl'];
              $new_app_url = esc_attr( get_option('app_url') );

              $app_android_url = esc_attr( get_option('app_android_url') );

              $app_icon = $data['results'][0]['artworkUrl512'];
              $new_app_icon = esc_attr( get_option('app_icon') );

              $app_name = $data["results"][0]["trackName"];
              $new_app_name = esc_attr( get_option("app_name") );

              $app_description = $data['results'][0]['description'];
              $new_app_description = esc_attr( get_option('app_description') );

              $app_price = $data['results'][0]['price'];
              $new_app_price = esc_attr( get_option('app_price') );

              $app_currency = $data['results'][0]['currency'];

              $app_screens = $data['results'][0]['screenshotUrls'];

              $app_tagline = esc_attr( get_option('app_tagline') );
              $app_upload_01 =  esc_attr( get_option('app_upload_01') );
              $app_primary_color =  esc_attr( get_option('app_primary_color') );
              $app_font_color = esc_attr( get_option('app_font_color') );
              $app_background_color = esc_attr( get_option('app_background_color') );
              $app_mobile_device = esc_attr( get_option('app_mobile_device') );

              $app_facebook_url = esc_attr( get_option('app_facebook_url') );
              $app_twitter_url = esc_attr( get_option('app_twitter_url') );
              $app_instagram_url = esc_attr( get_option('app_instagram_url') );

              $editor_id = 'wp-launchapp-editor';

            ?>

        </div>

        <div class="admin-block">
          
          <h3>App Details</h3>

          <hr>

          <table class="form-table">
            <tr valign="top">
              <th scope="row">App Icon<br><small>[appstore type="icon"]</small></th>
              <td>

              <?php if (esc_attr(get_option('app_icon')) == FALSE ) : ?>

              <img src="<?php echo $app_icon; ?>" style="height:100px;"> <br>
              <input type="text" name="app_icon" value="<?php echo $app_icon; ?>" />
              
              <?php else : ?>

              <img src="<?php echo $new_app_icon; ?>" style="height:100px;"> <br>
              <input type="text" name="app_icon" value="<?php echo $new_app_icon; ?>" />   

              <?php endif; ?>           

              </td>
            </tr>
            <tr valign="top">
              <th scope="row">App Store Url<br><small>[appstore type="url"]</small></th>
              <td>

              <?php if (esc_attr( get_option('app_url')) == FALSE ) : ?>

              <input type="text" name="app_url" style="width:100%;" value="<?php echo $app_url; ?>" />
              
              <?php else : ?>

              <input type="text" name="app_url" style="width:100%;" value="<?php echo $new_app_url; ?>" />   

              <?php endif; ?>           

              </td>
            </tr>
            <tr valign="top">
              <th scope="row">Google Play Store Url<br><small>[appstore type="android"]</small></th>
              <td>

              <input type="text" name="app_android_url" style="width:100%;" value="<?php echo $app_android_url; ?>" />

              </td>
            </tr>
            <tr valign="top">
              <th scope="row">App Name<br><small>[appstore type="name"]</small></th>
              <?php if (esc_attr( get_option('app_name')) == FALSE ) : ?>

                <td><input type="text" name="app_name" style="width:100%;" value="<?php echo $app_name; ?>"/></td>
                
              <?php else : ?>

                <td><input type="text" name="app_name" style="width:100%;" value="<?php echo $new_app_name; ?>"/></td>

              <?php endif; ?>
              
            </tr>
            <tr valign="top">
              <th scope="row">App Tagline<br><small>[appstore type="tagline"]</small></th>
              <td><input type="text" name="app_tagline" style='width:100%;' value="<?php echo $app_tagline; ?>" /></td>
            </tr>
            <tr valign="top">
              <th scope="row">App Description<br><small>[appstore type="description"]</small></th>
              
              <td>
              <?php if (esc_attr( get_option('app_description')) == FALSE ) : ?>
                  
                  <textarea name="app_description" rows="5" style="width:100%;"><?php echo $app_description; ?></textarea>
                  <?php // wp_editor($app_description, $editor_id, $settings = array('textarea_name'=>'app_description','textarea_rows'=>10, 'media_buttons' => false)); ?>

              <?php else : ?>
                  
                  <textarea name="app_description" rows="5" style="width:100%;"><?php echo $new_app_description; ?></textarea>
                  <?php // wp_editor(htmlspecialchars_decode($new_app_description), $editor_id, $settings = array('textarea_name'=>'app_description','textarea_rows'=>10, 'media_buttons' => false)); ?>

              <?php endif; ?>
              </td>

            </tr>
            <tr valign="top">
              <th scope="row">App Price<br><small>[appstore type="currency"][appstore type="price"]</small></th>

              <td><input type='text' name='app_currency' style='width:80px;' value='<?php echo $app_currency; ?>' disabled />

              <?php if (esc_attr( get_option('app_price')) == FALSE ) : ?>

                <input type='text' name='app_price' value='<?php echo $app_price; ?>'/>
                
                <?php else : ?>

                <input type='text' name='app_price' value='<?php echo $new_app_price; ?>'/>

                <?php endif; ?>

                </td>
              
            </tr>
            <tr valign="top">
              <th scope="row">App Screenshots<br><small>You can use direct links to the images</small></th>
              <td>
                <?php foreach ($app_screens as $app_screen) : ?>
                <ul style='list-style-type:none;padding:0;margin:0;'>
                <li style='display:inline-block;float:left;'><img src='<?php echo $app_screen; ?>' style='height:200px;'>
                <input type="text" value="<?php echo $app_screen; ?>"></li>
                </ul>
                <?php endforeach; ?> 
              </td>
            </tr>
          </table>

        </div>
        
        <p>&nbsp;</p>

        <h3>Advanced Settings</h3>
        <p>Customising these values are only needed if you are using <b>WP App Store Landing Page</b> theme.</p>

        <div class="admin-block">

          <h3>Visual Styling</h3>

          <hr>
      
          <table class="form-table">
            <tr valign="top">
              <th scope="row">Mobile Device</th>
              <td> 
                <select name="app_mobile_device">
                  <option value="iphone-wireframe">Wireframe</option>
                  <option value="iphone-device-gold">Gold</option>
                  <option value="iphone-device-silver">Silver</option>
                  <option value="iphone-device">Space Gray</option>
                </select>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">Background Image<br><small>Background color will be used if left blank</small></th>
              <td>
                <label for="upload_image">
                  <input id="upload_image" type="text" size="36" name="app_upload_01" value="<?php echo $app_upload_01; ?>" /> 
                  <input id="upload_image_button" class="button" type="button" value="Add from Media Library" />
                </label>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">Background Color<br><small>Hex code (#FFFFFF)</small></th>
              <td> 

              <?php if (esc_attr( get_option('app_background_color')) == FALSE ) : ?>

              <input type="text" name="app_background_color" value="#FFFFFF" /> <div style="background-color:#FFFFFF;width:160px;height:30px;border:1px solid #EEEEEE;"></div>

              <?php else : ?>

              <input type="text" name="app_background_color" value="<?php echo $app_background_color; ?>" /> <div style="background-color:<?php echo $app_background_color; ?>;width:160px;height:30px;border:1px solid #EEEEEE;"></div>

              <?php endif; ?>

              </td>
            </tr>
            <tr valign="top">
              <th scope="row">Link Color<br><small>Hex code (#e70972)</small></th>
              <td> 

              <?php if (esc_attr( get_option('app_primary_color')) == FALSE ) : ?>

              <input type="text" name="app_primary_color" value="#000000" /> <div style="background-color:#000000;width:160px;height:30px;border:1px solid #EEEEEE;"></div>

              <?php else : ?>

              <input type="text" name="app_primary_color" value="<?php echo $app_primary_color; ?>" /> <div style="background-color:<?php echo $app_primary_color; ?>;width:160px;height:30px;border:1px solid #EEEEEE;"></div>

              <?php endif; ?>

              </td>
            </tr>
            <tr valign="top">
              <th scope="row">Font Color<br><small>Hex code (#333333)</small></th>
              <td> 

              <?php if (esc_attr( get_option('app_font_color')) == FALSE ) : ?>

              <input type="text" name="app_font_color" value="#333333" /> <div style="background-color:#333333;width:160px;height:30px;border:1px solid #eee;"></div>

              <?php else : ?>

              <input type="text" name="app_font_color" value="<?php echo $app_font_color; ?>" /> <div style="background-color:<?php echo $app_font_color; ?>;width:160px;height:30px;border:1px solid #eee;"></div>

              <?php endif; ?>

              </td>
            </tr>
          </table>

        </div>

        <div style="background-color:white;padding:1em;margin:1em 0;border:1px solid #ddd;">

          <h3>Social Media Settings</h3>

          <hr>
      
          <table class="form-table">
            <tr valign="top">
              <th scope="row">Facebook</th>
              <td> <input type="text" name="app_facebook_url" style="width:100%;" value="<?php echo $app_facebook_url; ?>" /></td>
            </tr>
            <tr valign="top">
              <th scope="row">Twitter</th>
              <td> <input type="text" name="app_twitter_url" style="width:100%;" value="<?php echo $app_twitter_url; ?>" /></td>
            </tr>
            <tr valign="top">
              <th scope="row">Instagram</th>
              <td> <input type="text" name="app_instagram_url" style="width:100%;" value="<?php echo $app_instagram_url; ?>" /></td>
            </tr>
          </table>

        </div>

            <?php

          }

        } else { 

          echo "Whoops! The app you are looking for does not exists!";

        }

      }

      ?>
      
      

    <?php submit_button(); ?>

  </form>

</div>

<?php if (wp_get_theme() != 'WP Landing Page') : ?>

<div class="wrap" style="width:24%;float:right;top:55px;padding:10px;background-color:#e74c3c;color:white;border:1px solid #c0392b;position:fixed;right:0;text-align:center;">

  Download <a href="http://lioncitylab.com/wordpress/" target="_blank" style="font-weight:bold;color:white;">WP Landing Page</a> theme

</div>

<?php else : ?>

<div class="wrap" style="width:24%;float:right;margin-top:32px;padding:10px;background-color:#2ecc71;color:white;border:1px solid #27ae60;position:fixed;right:0;text-align:center;">

  <b><?php echo wp_get_theme(); ?></b> theme is activated

</div> 

<?php endif; ?>


<div class="wrap" style="width:24%;float:right;top:115px;padding:10px;background-color:white;border:1px solid #ddd;position:fixed;right:0;">

  <h2>WP App Store API</h2>

  <hr>

  <p><strong>Version 1.0.3</strong> (23 Apr 2016)</p>

  <ul>
    <li> - Initial Release</li>
    <li> - Added basic features</li>
    <li> - Added shortcode features</li>
  </ul>

  <hr>

  <p>More info: <a href="http://lioncitylab.com/wordpress" target="_blank">Lioncitylab</a></p>
  
  <a href="http://goo.gl/forms/ogXKwcI8dT" class="button" target="_blank">Submit New Request</a>

</div>
<?php 

} // end of wp_appstore_api_settings_page() 

//Shortcodes functions and information
function appstore_api_callback($atts,$content,$tag){
  
  //collect values, combining passed in values and defaults
  $values = shortcode_atts(array(
    'type' => 'other'
  ),$atts);  
  
  //based on input determine what to return
  $output = '';
  if($values['type'] == 'name'){
    $output = esc_attr(get_option("app_name"));
  }
  else if($values['type'] == 'icon'){
    $output = esc_attr(get_option("app_icon"));
  }
  else if($values['type'] == 'url'){
    $output = esc_attr(get_option("app_url"));
  }
  else if($values['type'] == 'description'){
    $output = esc_attr(get_option("app_description"));
  }
  else if($values['type'] == 'id'){
    $output = esc_attr(get_option("itunes_id"));
  }
  else if($values['type'] == 'android'){
    $output = esc_attr(get_option("app_android_url"));
  }
  else if($values['type'] == 'tagline'){
    $output = esc_attr(get_option("app_tagline"));
  }
  else if($values['type'] == 'price'){
    $output = esc_attr(get_option("app_price"));
  }
  else if($values['type'] == 'currency'){
    $output = esc_attr(get_option("app_currency"));
  }
  else{
    $output = 'Please include a type to display its content'; 
  }
  
  return $output;
  
}

add_shortcode('appstore', 'appstore_api_callback');
