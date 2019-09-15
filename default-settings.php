<?php
function Get_Default_Settings() {
 $defaults=array(
   'latest_entry_id' => '1',
   'current_city_id' => '1',
   'today_date', current_time('d/m/Y'),
   'current_city' => 'Washington DC',
   'current_country' => 'USA',
   'current_region' => 'Washington',
   'current_population' => '9,665,892',
   'current_timezone' => 'UTC−5',
   'current_details' => 'The capital of the United States of America.',
   'current_image_url' => 'WashingtonDC.jpg',
 );
 return $defaults;
}
