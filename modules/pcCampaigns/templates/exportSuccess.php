<?php
  $content = $codes->getRawValue();
  $outstream = fopen('php://output', 'w');
  
  $charset = sfConfig::get('software_internals_charset');
  $delimiter = ",";
  
  $header = [];
  foreach ($content[0] as $key => $value) {
    $header[] = __($key, null, 'pc_campaigns');
  }
  
  if ( $format )
  {
    $l = '"' . implode('";"', $header) . '"' . PHP_EOL;
    fputs($outstream, mb_convert_encoding($l, 'WINDOWS-1252', $charset['db']));
  }
  else
  {
    fputcsv($outstream, $header, $delimiter, '"');
  }

  
  foreach ($content as $key => $line) {
    if ( $format )
    {
      $l = '"' . implode('";"', $line) . '"' . PHP_EOL;
      fputs($outstream, mb_convert_encoding($l, 'WINDOWS-1252', $charset['db']));
    }
    else
    {
      fputcsv($outstream, $line, $delimiter, '"');
    }
  }

  fclose($outstream);