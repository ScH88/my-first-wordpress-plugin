<?php
function Add_To_Settings_Tab(){
  //Add a COTD Page to the "settings" tab
  add_options_page(
    'COTD Settings Page',
    'City Of The Day',
    'manage_options',
    'cotd-settings-page',
    'Display_Settings_Page'
  );
  //If a post containing a field named "new_random_city" is sent to this page
  if (isset($_POST['new_random_city'])) {
    //add_action("admin_head-$page", 'Refresh_Button_Clicked');
    //Check the admin referer
    check_admin_referer('cotd-update-settings');
    //Call the Parse_CSV_Database method
    Parse_CSV_Database();
  }
}
function Display_Settings_Page() {
  //Check if the Cities.csv file exists
  Check_For_CSV_File();
  //Create a new Form instance
  $form = new Cotd_Form(Get_Cotd_Settings());
  //Retrieve the "cotd-settings" array from the wp_options table
  $currSettings = Get_Cotd_Settings();
  ?>
  <div class="wrap">
    <h1>Settings - City Of The Day</h1>
    <br/>
    <p>Today's Date: <?php echo $currSettings['today_date']; ?></p>
    <p>ID Of Latest Database Entry: <?php echo $currSettings['latest_entry_id']; ?></p>
    <br/>
    <h2>Current City: <?php echo $currSettings['current_city'] . ", " . $currSettings['current_country']; ?></h2>
    <?php if ($currSettings['today_date'] == "None") : ?>
      <p>Image URL not available</p>
    <?php else : ?>
      <img src="<?php echo  plugin_dir_url(__FILE__) . 'images/' . $currSettings['current_image_url']; ?>" class="city_img"
      alt="<?php echo $currSettings['current_city'] . ", " . $currSettings['current_country']; ?>"/>
    <?php endif; ?>
    <p>City ID: <?php echo $currSettings['current_city_id']; ?></p>
    <p>Region: <?php echo $currSettings['current_region']; ?></p>
    <p>Population: <?php echo $currSettings['current_population']; ?></p>
    <p>Time Zone: <?php echo $currSettings['current_timezone']; ?></p>
    <p>Details: <?php echo $currSettings['current_details']; ?></p>
    <br/>
    <form method="post", action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <?php wp_nonce_field('cotd-update-settings'); ?>
      <p class="submit">
        <input type="submit" name="new_random_city" class="button-primary" value="New Random City"/>
      </p>
      <?php $form->add_used_inputs(); ?>
    </form>
  </div>
  <?php
}
//function Refresh_Button_Clicked() {
  //check_admin_referer('cotd-update-settings');
  //$settings = Cotd_Form::get_post_data();
  //Update_Cotd_Settings($settings);
//}
