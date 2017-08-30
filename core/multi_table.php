<?php
  // retrieve the table and key from the path
  $main_table = preg_replace('/[^a-z0-9_]+/i', '', $request[1]);
  $related_table = preg_replace('/[^a-z0-9_]+/i', '', $request[3]);
  header('Content-Type: application/json');
  if($main_table == "") {
    error_response(501, 'Invalid endpoint!');
    return;
  }

  if(!empty($request[2])) {
    $key = $request[2] ? $request[2] : "";
  }
  if(!empty($request[4])) {
    $secondary_key = $request[4] ? $request[4] : "";
  }

  // escape the columns and values from the input object
  $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
  $values = array_map(function ($value) use ($link) {
    if ($value === null) {
      return null;
    }
    return mysqli_real_escape_string($link, (string)$value);
  }, array_values($input));
  $req = array_combine($columns, $values);
  
  // build the SET part of the SQL command
  $set = '';
  for ($i=0; $i<count($columns); $i++) {
    $set .= ($i>0?',':'').'`'.$columns[$i].'`=';
    $set .= ($values[$i] === null ? 'NULL':'"'.$values[$i].'"');
  }
  
  // create SQL based on HTTP method
    
  switch ($method) {
    case 'GET':
      $sql = "SELECT `$related_table`.*
              FROM `$related_table`
              LEFT JOIN `$main_table"."_"."$related_table` ON `$related_table`.`id` = `$main_table"."_"."$related_table`.`$related_table"."_id`"." 
              LEFT JOIN `$main_table` ON `$main_table"."_"."$related_table`.`$main_table"."_id`" ." = `$main_table`".".`id`
              WHERE `$main_table`.id = $key";
      break;

    case 'POST':
      $sql = "INSERT INTO `$main_table"."_"."$related_table` (`$main_table"."_id`" . ", `$related_table"."_id`" . ") VALUES ($key, " . $req["id"] . ")";
      break;

    case 'DELETE':
      if(check_token()) {
        $sql = "DELETE FROM `$main_table"."_"."$related_table` 
                WHERE `$main_table" . "_id`='$key'
                AND `$related_table" . "_id`='$secondary_key'";
      }
      break;

    default:
      error_response(405, 'Only GET and DELETE methods are allowed!');
      break;
  }
  
  // excecute SQL statement
  $result = mysqli_query($link, $sql);

  // ERRORE

  // die if SQL statement failed
  if (!$result) {
    error_response(501, 'Invalid Endpoint! No table founded with this name: '.$table);
    return;
  }

  // print results, insert id or affected row count
  switch ($method) {
    case 'GET':
      if(mysqli_affected_rows($link) == 0) {
        if($key == null) {
          echo '[]';
          http_response_code(200);
        }
        else {
          error_response(404, "The table $main_table with this id ($key) don't have $related_table");
        }
      }
      elseif(mysqli_affected_rows($link) == 1 && $key) {
        echo(json_encode(mysqli_fetch_object($result), 128));
      }
      else {
        echo '[';
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
          echo ($i > 0 ? ',' : '').json_encode(mysqli_fetch_object($result));
        }
        echo ']';
      }
      break;
    
    case 'POST':
      echo(json_encode(array('count' => mysqli_affected_rows($link))));
      break;

    default:
      echo(json_encode(array('count' => mysqli_affected_rows($link))));
      break;
  }