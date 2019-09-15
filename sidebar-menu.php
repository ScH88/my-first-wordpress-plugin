<?php
//Function for creating the content for when the main plugin's sidebar menu tab is clicked
function createMainPluginIndex() {
    //End php formatting in order to write the corresponding HTML content to page. Once finished, end HTML and return back to php
    ?>
    <div style='text-align:center;'>
        <h1>Welcome to the Automatic Content Generator</h1>
        <div class="img_container">
          <img src="<?php echo plugin_dir_url(__FILE__); ?>images/city.jpg" class="city_img" alt="Welcome City Img"/>
        </div>
    </div>
    <p>The purpose of this plugin is to allow a page to display the details of a different city every day
        This is achieved by interacting with a database to retrieve values. You can also add a new city, so feel free.</p>
    <p><strong>Shortcode to copy & pase (CTRL + C):</strong> [DailyCityPluginContent]</p>
    <?php
}
function addNewCity() {
  //Check if there is a Cities.csv file
  Check_For_CSV_File();
  //If an array object with a key of "submit_city_details" exists in a post object sent to this page
  //(i.e. if the user submits the form in this method)
  //If the condition is met, break the current format (php) to write the corresponding HTML code....
  //...then begin php formatting again in order to end the if-statement
  if (array_key_exists('submit_city_details', $_POST)) {
    //Create a file handle variable by calling fopen to find the Cities csv file, then "a" to create/append
    //If the Cities.csv file exists
    $fileHandle = fopen(dirname(__FILE__) . "/Cities.csv", "a");
    //Define a "latestId" variable for storing the new Id, which is the plugins latest_id value plus 1
    $newId = (Get_Cotd_Settings('latest_entry_id') + 1);
    //Variable for the name of the image/lack of
    $imgName = "None";
    //If there is no files attached to the post data
    if (empty($_FILES["add_img_input"])) {
      //Set the image url field of the edited entry to "None"
      $imgName = "None";
    //Otherwise
    } else {
      //Get the image's name from the file
      $imgName = $_FILES['add_img_input']['name'];
      //Move the uploaded file to the images directory
      move_uploaded_file($_FILES["add_img_input"]["tmp_name"], dirname(__FILE__) . "\images\\" . $imgName);
    }
    //Create an array containg the new Id variable and all form input data
    $newLine = array(
      "latest_entry_id" => $newId, "current_city" => $_POST['city_input'], "current_country" => $_POST['country_input'],
      "current_region" => $_POST['region_input'], "current_population" => $_POST['population_input'], "current_timezone" => $_POST['timezone_input'],
      "current_details" => $_POST['details_input'], 'current_image_url' => $imgName
    );
    //Use fputcsv to append the row of data (2nd argument) to the file (1st argument)
    fputcsv($fileHandle, $newLine);
    //Update this plugin's value for the id of the most recent city, as the $newId value defined earlier
    Update_Cotd_Single_Setting('latest_entry_id', $newId);
    //Close the file handle
    fclose($fileHandle);
  }
  //Create a new Form instance
  $form = new Cotd_Form();
  ?>
  <?php if (isset($_GET['updated'])) : ?>
  <div id="message" class="updated fade">
    <p><strong>Your new city has been added</strong></p>
  </div>
<?php endif; ?>
<div class="wrap">
  <h2>Add City To Database</h2>
  <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>", enctype="multipart/form-data">
    <?php wp_nonce_field('cotd-update-settings'); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="city_input">City: </label></th>
        <td>
          <?php $form->add_text('city_input', array('class' => 'large-text', 'maxlength' => '50', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="country_input">Country: </label></th>
        <td>
          <?php $form->add_text('country_input', array('class' => 'large-text', 'maxlength' => '50', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="region_input">Region: </label></th>
        <td>
          <?php $form->add_text('region_input', array('class' => 'large-text', 'maxlength' => '50', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="population_input">Population: </label></th>
        <td>
          <?php $form->add_numeric('population_input', array('class' => 'large-text', 'maxlength' => '15', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="timezone_input">Time Zone: </label></th>
        <td>
          <?php $form->add_text('timezone_input', array('class' => 'large-text', 'maxlength' => '10', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="details_input">Details: </label></th>
        <td>
          <?php $form->add_textarea('details_input', array('rows' => '5', 'cols' => '100', 'maxlength' => '500', 'required' => null)); ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="add_img_input">Image: </label></th>
        <td>
          <?php $form->add_file('add_img_input', array('class' => 'large-text', 'accept' => 'image/*')); ?>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="submit_city_details" class="button-primary" value="Create New City" />
    </p>
  </form>
</div>
<?php
}
function deleteCity() {
  //Check if there is a Cities.csv file
  Check_For_CSV_File();
  //Array which will be used to store all retrieved lines in the Cities csv file
  $entries = [];
  //Create a handle pointing to the Cities.csv file, with "r" permission so that it reads only
  $handle = fopen(dirname(__FILE__) . "\Cities.csv", 'r');
  //Boolean for if the handler is on the first line
  $is_first_line = true;
  //While there is no end of file from the beginning to the end of the file handler
  while (!feof($handle)) {
    //Get the current line of the file handler
    $line = fgetcsv($handle);
    //If the first entry in the current line contains any value (i.e. if the line is not blank)
    if ($is_first_line == true) {
      //Set the first line variable to false
      $is_first_line = false;
      //Go straight to the next loop
      continue;
    }
    //If the user has submitted a form object containing a "delete_city_details" submit value, and...
    //...the post object's "delId" input value is the same as the first entry of the current line (the id)
    if ($_POST['delId'] === $line[0]) {
      //Get the path of the image to delete
      $oldImg = dirname(__FILE__) . "\images\\" . $line[7];
      //If the image exists
      if (file_exists($oldImg)) {
        //Delete the image
        unlink($oldImg);
      }
      //Escape this iteration and continue on to the next one
      continue;
    } else {
      //Add the current line/array to the $entries array
      $entries[] = $line;
    }
  }
  //Close the current handler
  fclose($handle);
  //If the user has submitted a post object containing a "delete_city_details" submit value
  if (array_key_exists('delete_city_details', $_POST)) {
    //Call the rewriteCSV method and pass it the data array, which will rewrite the Cities.csv file with the change made
    rewriteCSV($entries);
    //If this plugin's cotd-settings array's 'current_city_id' value is the same os the one sent to the post
    if (Get_Cotd_Settings('current_city_id') == $_POST['delId']) {
      //Call the Parse_CSV_Database method to select a new daily city
      Parse_CSV_Database();
    }
    ?>
    <div id="message" class="updated fade">
      <p><strong>City successfully deleted; ID - <?php echo $_POST['delId']; ?></strong></p>
    </div>
    <?php
  }
  //Create a new Form instance
  $form = new Cotd_Form();
  ?>
  <div class="wrap">
    <h1>Delete City</h1>
    <br/>
    <?php if (sizeof($entries) > 1) :?>
    <form method="post", action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <?php wp_nonce_field('cotd-update-settings'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">
            <label for="delId">Select City To Delete: </label>
          </th>
          <td>
            <?php $form->add_select('delId', GetValuesForDropdown($entries)); ?>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" name="delete_city_details" class=" button button-primary" value="DELETE" />
      </p>
      <?php $form->add_used_inputs(); ?>
    </form>
    <?php else : ?>
      <p>It appears that there is only one entry, so deleting it can only make things awkward.</p>
    <?php endif; ?>
  </div>
  <?php
}
function editCity() {
  //Check if there is a Cities.csv file
  Check_For_CSV_File();
  //Create a handle pointing to the Cities.csv file, with "r" permission so that it reads only
  //If the file handle has successfully acquired the Cities.csv file
  $handle = fopen(dirname(__FILE__) . "\Cities.csv", 'r');
  //Array which will be used to store all retrieved lines in the Cities csv file
  $entries = [];
  //Variable for if the handle is on the first
  $is_first_line = true;
  //While there is no end of file from the beginning to the end of the file handler
  while (!feof($handle)) {
    //Get the current line of the file handler
    $line = fgetcsv($handle);
    //If the first entry in the current line contains any value (i.e. if the line is not blank)
    if ($is_first_line == true) {
      //Set the first line variable to false
      $is_first_line = false;
      //Go straight to the next loop
      continue;
    }
    //If the user has submitted a form object containing a "edit_city_details" submit value, and...
    //...the post object's "editId" input value is the same as the first entry of the current line (the id)
    if (array_key_exists('edit_city_details', $_POST) && $_POST['editId'] === $line[0]) {
      //Change the second value of the current line (city) as the post object's city_input value
      $line[1] = $_POST['city_input'];
      //Change the third value of the current line (country) as the post object's country_input value
      $line[2] = $_POST['country_input'];
      //Change the fourth value of the current line (region) as the post object's region_input value
      $line[3] = $_POST['region_input'];
      //Change the fifth value of the current line (population) as the post object's population_input value
      $line[4] = $_POST['population_input'];
      //Change the sixth value of the current line (timezone) as the post object's timezone_input value
      $line[5] = $_POST['timezone_input'];
      //Change the seventh value of the current line (details) as the post object's details_input value
      $line[6] = $_POST['details_input'];
      //Get the path of the old image to delete
      $oldImg = dirname(__FILE__) . "\images\\" . $line[7];
      //If the image exists
      if (file_exists($oldImg)) {
        //Delete the image
        unlink($oldImg);
      }
      //If there is no files attached to the post data
      if (empty($_FILES["img_input"])) {
        //Set the image url field of the edited entry to "None"
        $line[7] = "None";
      //Otherwise
      } else {
        //Get the image's name from the file
        $imgName = $_FILES['img_input']['name'];
        //Move the uploaded file to the images directory
        move_uploaded_file($_FILES["img_input"]["tmp_name"], dirname(__FILE__) . "\images\\" . $imgName);
        //Set the value of the current line's 7th (url) field as the image name
        $line[7] = $imgName;
      }
    }
    //Add the current line/array to the $entries array
    $entries[] = $line;
  }
  //Close the current handler
  fclose($handle);
  //If the user has submitted a post object containing a "edit_city_details" submit value
  if (array_key_exists('edit_city_details', $_POST)) {
    //Call the rewriteCSV method and pass it the $entries array, which will rewrite the Cities.csv file with the change made
    rewriteCSV($entries);
    ?>
    <div id="message" class="updated fade">
      <p><strong>City successfully Edited;  <?php echo $_POST['city_input']; ?>, <?php echo $_POST['country_input']; ?></strong></p>
    </div>
    <?php
  }
  //Create a new Form instance
  $form = new Cotd_Form();
  ?>
  <div class="wrap">
    <h1>Edit City</h1>
    <br/>
    <form method="post", action="<?php echo $_SERVER['REQUEST_URI']; ?>", enctype="multipart/form-data">
      <?php wp_nonce_field('cotd-update-settings'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">
            <label for="editId">Select City To Edit: </label>
          </th>
          <td>
            <?php $form->add_select('editId', GetValuesForDropdown($entries), array('onchange' => 'Set_Current_Entry(this.value)')); ?>
           </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="city_input">City: </label></th>
          <td>
            <?php $form->add_text('city_input', array('class' => 'large-text', 'value' => $entries[0][1], 'maxlength' => '50', 'required' => null)); ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="country_input">Country: </label></th>
          <td>
            <?php $form->add_text('country_input', array('class' => 'large-text', 'value' => $entries[0][2], 'maxlength' => '50', 'required' => null)); ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="region_input">Region: </label></th>
          <td>
            <?php $form->add_text('region_input', array('class' => 'large-text', 'value' => $entries[0][3], 'maxlength' => '50', 'required' => null)); ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="population_input">Population: </label></th>
          <td>
            <?php $form->add_text('population_input', array('class' => 'large-text', 'value' => $entries[0][4], 'maxlength' => '15', 'required' => null)); ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="timezone_input">Time Zone: </label></th>
          <td>
            <?php $form->add_text('timezone_input', array('class' => 'large-text', 'value' => $entries[0][5], 'maxlength' => '10', 'required' => null)); ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="details_input">Details: </label></th>
          <td>
            <?php $form->add_textarea('details_input', array('rows' => '5', 'cols' => '100', 'value' => $entries[0][6], 'maxlength' => '500', 'required' => null)); ?>
          </td>
        </tr>
        <?php if ($entries[0][7] != null): ?>
        <tr valign="top">
          <th scope="row"><label for="img_preview">Current Image: </label></th>
          <td>
            <?php $form->add_image('img_preview', plugin_dir_url(__FILE__).'images/'.$entries[0][7],
            array('class' => 'city_img', 'value' => $entries[0][1] . ', ' . $entries[0][2])); ?>
          </td>
        </tr>
      <?php endif; ?>
        <tr valign="top">
          <th scope="row"><label for="img_input">Image: </label></th>
          <td>
            <?php $form->add_file('img_input', array('class' => 'large-text', 'accept' => 'image/*')); ?>
          </td>
        </tr>
      </table>
      <?php $form->add_hidden('all_entries', $entries); ?>
      <p class="submit">
        <input type="submit" name="edit_city_details" class=" button button-primary" value="Edit Form" />
      </p>
      <?php $form->add_used_inputs(); ?>
    </form>
  </div>
  <script type="text/javascript">
  //Variable for storing an array of the currently selected city
    var entryArray;
    //Function for if the value of the drop-down menu is changed
    function Set_Current_Entry(val) {
      //Get a line from the $entries php array according to the value sent to this method (minus 1, due to array being zero-based)
      var entryArray = <?php echo json_encode($entries); ?>[val - 1];
      //Change the value of the input field whose id is "city_input", setting it as the 1st entry in the curren array (city)
      document.getElementById('city_input').value = entryArray[1];
      //Change the value of the input field whose id is "country_input", setting it as the 1st entry in the curren array (country)
      document.getElementById('country_input').value = entryArray[2];
      //Change the value of the input field whose id is "region_input", setting it as the 1st entry in the curren array (region)
      document.getElementById('region_input').value = entryArray[3];
      //Change the value of the input field whose id is "population_input", setting it as the 1st entry in the curren array (population)
      document.getElementById('population_input').value = entryArray[4];
      //Change the value of the input field whose id is "timezone_input", setting it as the 1st entry in the curren array (time zone)
      document.getElementById('timezone_input').value = entryArray[5];
      //Change the value of the input field whose id is "details_input", setting it as the 1st entry in the curren array (details)
      document.getElementById('details_input').value = entryArray[6];
      //If the Image url value equals "None"
      if (entryArray[7] != "None") {
        //Change the value of the picture preview src
        document.getElementById('img_preview').src = "<?php echo plugin_dir_url(__FILE__); ?>images/" + entryArray[7];
      };
    };
  </script>
  <?php
}
