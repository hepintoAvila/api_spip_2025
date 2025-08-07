<?php

function inc_importer_csv_dist($file, $options = array()) {
  $default_options = array(
    'head' => false,
    'delim' => ',',
    'enclos' => '"',
    'len' => 10000,
    'charset_source' => '',
  );

  $options = array_merge($default_options, $options);

  $data = array();
  if (($handle = fopen($file, 'r')) !== FALSE) {
    if ($options['head']) {
      $header = fgetcsv($handle, $options['len'], $options['delim'], $options['enclos']);
      $header = array_map('trim', $header);
    }

    while (($row = fgetcsv($handle, $options['len'], $options['delim'], $options['enclos'])) !== FALSE) {
      if ($options['head']) {
        $row = array_combine($header, $row);
      }
      $data[] = $row;
    }
    fclose($handle);
  }

  return $data;
}