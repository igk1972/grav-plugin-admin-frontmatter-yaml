<?php


function array_diff_assoc_recursive($array1, $array2) {
  $difference = [];
  foreach ($array1 as $key => $value) {
    if ( is_array($value) ) {
      if ( !isset($array2[$key]) || !is_array($array2[$key]) ) {
        $difference[$key] = $value;
      } else {
        $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
        if ( !empty($new_diff) ) {
          $difference[$key] = $new_diff;
        }
      }
    } else if ( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
      $difference[$key] = $value;
    }
  }
  return $difference;
}


function get_array_by_key_path($data, $key_paths) {
  if (count($key_paths) == 0){
    return $data;
  }
  $key = array_shift($key_paths);
  if ($key === '[]') {
    $_ = array_merge([], $key_paths);
    $key = array_shift($_);
    $result = [];
    foreach ($data as $index=>$value) {
      $result[$index][$key] = get_array_by_key_path($value, $key_paths);
    }
    if (count($result) > 0) {
      return $result;
    }
  } else {
    if (isset($data[$key])) {
      return get_array_by_key_path($data[$key], $key_paths);
    }
  }
}


function array_merge_recursive_distinct(array $array1, $array2 = null) {
  $merged = $array1;
  if (is_array($array2)) {
    foreach ($array2 as $key => $val) {
      if (is_array($array2[$key])) {
        $merged[$key] = is_array($merged[$key]) ? array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
      } else {
        $merged[$key] = $val;
      }
    }
  }
  return $merged;
}
