<?php
 
function inc_importer_csv_dist($file, $options = array()) {
  $default_options = array(
    'len' => PHP_INT_MAX, // Valor por defecto para cargar todos los registros
  );
  $options = array_merge($default_options, $options);

  $data = array();
  $handle = fopen($file, 'r');
  $header = fgetcsv($handle, 0, ';');
  $header = array_map(function($value) {
    return trim(str_replace(array("\xef\xbb\xbf", ' '), '', $value), " \t\n\r\0\x0B\"");
  }, $header);
  array_pop($header);

  $count = 0;
  while (($row = fgetcsv($handle, 0, ';')) !== FALSE && $count < $options['len']) {
    array_pop($row);
    $item = array();
    foreach ($header as $index => $key) {
      $item[$key] = isset($row[$index]) ? trim($row[$index], "\"") : '';
    }
    $data[] = $item;
    $count++;
  }
  fclose($handle);

  return $data;
}