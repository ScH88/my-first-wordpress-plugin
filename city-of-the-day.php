<?php
/**
 * @package CityOfTheDay
**/
/*
Plugin Name: City Of The Day
Plugin URI:
Description: Automated Content Generator for locations, for use in Wordpress
Version: 1.0
Author: ScH88
Author URI:
 */
 require_once(dirname(__FILE__).'\sidebar-menu.php');
 require_once(dirname(__FILE__).'\csv-parser.php');
 require_once(dirname(__FILE__).'\default-settings.php');
 require_once(dirname(__FILE__).'\form-class.php');
 require_once(dirname(__FILE__).'\settings-functions.php');
 require_once(dirname(__FILE__).'\settings-page.php');
 //Function for starting up all content upon plugin activation
 function Initialize_Plugin() {
     //Add an option to access this plugin and it' method-generated page via the dashboard sidebar
     //Arguments: (1) Title of Admin/Functions page, (2)Title in left sidebar menu in dashboard, (3)What users...
     //...have access,(4)Menu slug for url, (5) Callback function that returns the page content, (6) Menu Icon...
     //...URL (optional), (7) Position (optional)
     add_menu_page("City Of The Day", "COTD", 1, "city-of-the-day", "createMainPluginIndex");
     //Add a submenu option to the previously generated sidebar menu option leading to the plugin
     //Arguments: (1) Page title, (2)Page Title, (3) Repeat Page Title (4) No of users that have access...
     //(5) Menu slug for url, (6) Function that contains all the page, (7) Icon URL (optional), (8)Position (optional)
     add_submenu_page("city-of-the-day", "Add City", "Add City", 1, "add-city-to-database", "addNewCity");
     //Add a submenu option to the previously generated sidebar menu option leading to the plugin
     add_submenu_page("city-of-the-day", "Delete City", "Delete City", 1, "delete-city", "deleteCity");
     //Add a submenu option to the previously generated sidebar menu option leading to the plugin
     add_submenu_page("city-of-the-day", "Edit City", "Edit City", 1, "edit-city", "editCity");
 }
 //Function for printing the following content on screen when a page containing the respective shortcode is opened
 function Setup_Cities_Shortcode() {
   //Check for the Cities.csv file
   Check_For_CSV_File();
   //If the current (formatted) date is not the same as the one defined to this plugin
   if (current_time('d/m/Y') !== Get_Cotd_Settings('today_date')) {
     //Call the parseCSVDatabase method to randomly select a city and set it's values to this plugin
     Parse_CSV_Database();
   }
   $settings = Get_Cotd_Settings();
   //String for storing HTML page content
   $information = "";
   //Define a string object for storing HTML, starting with the page header
   $information = '<h1 style="text-align:center">City Of The Day</h1>';
   //
   if ($settings['current_image_url'] != "None") {
     $information .= '<div class="img_container">';
     $information .= '<img src="' . plugin_dir_url(__FILE__) . 'images/' . $settings['current_image_url'] . '" class="city_img" alt="Current City Img"/>';
     $information .= '</div>';
   }
   //Append a subheader to the HTML string, using get_option to retrieve the values of both the current city and country
   //get_option arguments: (1)Name of script variable, (2)What to return if the variable has no value
   $information .= "<h2>" . $settings['current_city'] . ", " . $settings['current_country'] . "</h2>";
   //Append a paragraph to the HTML string, using get_option to retrieve the values of the current region, population and timezone
   $information .= "<p><strong>Region:</strong> " . $settings['current_region'] . ", <strong>Population:</strong> "
   . $settings['current_population'] . ", <strong>Time Zone:</strong> " . $settings['current_timezone'] . "</p>";
   //Append a paragraph to the HTML string, using get_option to retrieve the value of the current city's details
   $information .= "<p>" . $settings['current_details'] . "</p>";
   //Return the HTML content value
   return $information;
 }
 function add_plugin_stylesheets() {
   //Create a new style pointing to the styles/mainstyle.css stylesheet file
   wp_register_style('mystylesheet', plugin_dir_url(__FILE__) . 'styles/mainstyle.css');
   //Enqueue the new stylesheet
   wp_enqueue_style('mystylesheet');
 }
 //function add_plugin_scripts() {
  //if (!is_admin()) {
    //wp_register_script('myjsscript', plugin_dir_url(__FILE__) . 'js/mainscript.css');
    //wp_enqueue_script('myjsscript');
  //}
 //}
 function Clean_Text($var) {
   //Trim all empty spaces
   $var = trim($var);
   //Remove all quote marks
   $var = stripslashes($var);
   //Convert special characters into HTML entities
   $var = htmlspecialchars($var);
   //Return the cleaned text
   return $var;
 }
//Upon printing Wordpress styles, call the add_plugin_stylesheets method to make the styles/mainstyle.css stylesheet be used in all pages
add_action('wp_print_styles', 'add_plugin_stylesheets');
//Upon printing Wordpress' JS scripts, call the add_plugin_scripts method to make the js/mainscript.js script file be used in all pages
//add_action('wp_print_js_scripts', 'add_plugin_scripts');
//Call add_action to hook the calling of the startContentGenerator method to "admin_menu" (calls upon loading the  admin menu)
add_action("admin_menu", "Initialize_Plugin");
//Call add_action to hook the calling of the add_to_settings_tab method to "admin_menu" (calls upon loading the  admin menu)
add_action('admin_menu', 'Add_To_Settings_Tab');
//Create the shortcode that can be used on pages and posts. To use this plugin, type "[DailyCityPluginContent]"...
//...in the page/post of choice in Wordpress' pages/posts editor section
//Arguments: (1)The name of this shortcode, (2)Main Function Name
//Note: Content of shortcode must never be printed
add_shortcode('DailyCityPluginContent', 'Setup_Cities_Shortcode');
