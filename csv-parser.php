<?php
function Check_For_CSV_File() {
  //If the Cities.csv file does not exist in the plugin root directory
  if (!file(plugin_dir_url(__FILE__) . "Cities.csv")) {
    //Call the Create_CSV_File method
    Create_CSV_File();
    //Call the Update_Cotd_Settings method
    Update_Cotd_Settings();
  }
}
function Create_CSV_File() {
  //Open a handler to the Cities.csv file
  $newfile = fopen(dirname(__FILE__) . "\Cities.csv", "wb");
  //Add the following headers to the CSV file
  fputcsv($newfile, array('ID','City','Country','Region/State','Population','Time Zone','Details','Image URL'));
  //Get the default settings from default-settings.php and store in an array
  $getDefaults = Get_Default_Settings();
  //Add a line containing the default array's id, city, country, region, population, timezone and details values
  fputcsv($newfile, array(Clean_Text($getDefaults['current_city_id']), Clean_Text($getDefaults['current_city']), Clean_Text($getDefaults['current_country']),
  Clean_Text($getDefaults['current_region']), Clean_Text($getDefaults['current_population']), Clean_Text($getDefaults['current_timezone']),
  Clean_Text($getDefaults['current_details']), Clean_Text($getDefaults['current_image_url'])));
  //CLose the file handler
  fclose($newfile);
}
//Function for parsing through a CSV file for storing cities and details, and retrieving a random entry
function Parse_CSV_Database() {
    //Array which will be used to store all retrieved lines in the Cities csv file
    $data = [];
    //Variable for what will be a random number, with a default of 0
    $SelectorNum = 0;
    //Boolean for if the handler is on the first line
    $is_first_line = true;
    //Handler to the contents of the "Cities" csv file
    $csvFile = fopen(dirname(__FILE__) . "\Cities.csv", 'r');
    //For each line in the csvFile array, the current one assigned to a variable called $line
    while (!feof($csvFile)) {
      //Get the current line of the file handler
      $line = fgetcsv($csvFile);
      //If the first entry in the current line contains any value (i.e. if the line is not blank)
      if ($is_first_line == true) {
        $is_first_line = false;
        continue;
      }
      if ($line[0] === Get_Cotd_Settings('current_city_id')) {
        continue;
      }
      if ($line != "") {
        //Use str_getcsv to parse the current line into an array (separated by commas), then add the array to $data
        $data[] = $line;
      }
    }
     //foreach ($data as $line) {
      // echo "<script type='text/javascript'>alert('$line[1]');</script>";
     //}
     if (sizeof($data) != 1) {
        //Define the randNo variable as a random number of between 0 and the size of the array...
       //...minus 1 to prevent it going one place past the final array index
       $SelectorNum = rand(0, (sizeOf($data) - 1));
       //This do-while loop will keep redefining the randNo variable while the randNo variable has the same value...
       //...as the current plugin's current_rand_index value
     }
    //Retrieve an array representing a city using the randomly generated number
    $randomCity = $data[$SelectorNum];
    //Create a blank array for updating this plugin's settings
    $settingArr = [];
    //Define the settings array's "today_date" value as the current datetime
    $settingArr['today_date'] = current_time('d/m/Y');
    //Add a key-pair entry into the array, with the key as "current_city_id" and it's value being the randomCity's ID field
    $settingArr['current_city_id'] = $randomCity[0];
    //Add a key-pair entry into the array, with the key as "current_city" and it's value being the randomCity's current_city field
    $settingArr['current_city'] = $randomCity[1];
    //Add a key-pair entry into the array, with the key as "current_countrycurrent_country" and it's value being the randomCity's current_country field
    $settingArr['current_country'] = $randomCity[2];
    //Add a key-pair entry into the array, with the key as "current_region" and it's value being the randomCity's current_region field
    $settingArr['current_region'] = $randomCity[3];
    //Add a key-pair entry into the array, with the key as "current_population" and it's value being the randomCity's current_population field
    $settingArr['current_population'] = $randomCity[4];
    //Add a key-pair entry into the array, with the key as "current_timezone" and it's value being the randomCity's current_timezone field
    $settingArr['current_timezone'] = $randomCity[5];
    //Add a key-pair entry into the array, with the key as "current_details" and it's value being the randomCity's current_details field
    $settingArr['current_details'] = $randomCity[6];
    //Add a key-pair entry into the array, with the key as "current_image_url" and it's value being the randomCity's image url field
    $settingArr['current_image_url'] = $randomCity[7];
    //Add a key-pair entry into the array, with the key as "latest_entry_id" and it's value being this plugin's 'cotd-settings' option's...
    //...latest_entry_id field, to prevent the default (1) being used instead
    $settingArr['latest_entry_id'] = Get_Cotd_Settings('latest_entry_id');
    //Pass the settings to the Update_Cotd_Settings method to update this plugin's settings with the values provided
    Update_Cotd_Settings($settingArr);
    //Close the file handler
    fclose($csvFile);
}
function rewriteCSV($citiesArray) {
  Check_For_CSV_File();
  //Create a new handle pointing to the same path, then select "w" to begin at 0, while truncuating...
  //...the file size to 0, allowing us to rewrite it's contents
  $handle = fopen(dirname(__FILE__) . "\Cities.csv", 'w');
  //Use fputcsv to only append the current line (2nd argument) to the file (1st argument)
  fputcsv($handle, array('id','City','Country','Region/State','Population','Time Zone','Details',"Image URL"));
  //For each object in array sent to the parameter, each one as $current with an index value called $key
  foreach ($citiesArray as $current) {
    //If the current line is not blank
    if ($current != "") {
      //Use fputcsv to append the current line(2nd argument) to the file (1st argument)
      fputcsv($handle, $current);
    }
  }
  //Close the file handler
  fclose($handle);
}
function GetValuesForDropdown($data) {
  //Variable for the array to return
  $returnArr = [];
  //For each line in the $data array, defined as "$current_val"
  foreach ($data as $current_val) {
    //If all of the first 3 entries are not blank
    if ($current_val[0] != "" && $current_val[1] != "" && $current_val[2] != "") {
      //Add to the return array variable a key of the first entry (id) and the paired value as a string combining the first 3 (id, city name and country)
      $returnArr[$current_val[0]] = $current_val[0] . ", " . $current_val[1] . ", " . $current_val[2];
    }
  }
  //Return the filled-in array
  return $returnArr;
}
