<h1>Uni-API</h1>

<?php

  require_once('config.php');
  require_once('core/data_type.php');

  $error_flag = FALSE;

  $link = mysqli_connect($host, $user, $pass);

  $models = file_get_contents('models.json');
  $encoded_models = json_decode($models, true);

  if(!$link) {
    die('Connection failed: ' . $link->connect_error);
  }

  echo('<ul>');

  // create DB
  $sql = 'CREATE DATABASE IF NOT EXISTS `' . $dbname .'`';
  if($link->query($sql) === TRUE) {
    echo("<li>Database <b>$dbname</b> created successfully!</li>");
  } else {
    echo('Error creating database: ' . $link -> error);
    echo($sql);
    $error_flag = TRUE;
    return;
  }
  $sql = "";

  // create TABLES
  foreach ($encoded_models['models'] as $model_name => $value) {
    echo("Creating $model_name ...");
    $sql .= " CREATE TABLE IF NOT EXISTS `$dbname`.`$model_name` ( ";
    foreach($value as $data => $data_type) {
      $sql .= "`$data`" . ($MYSQL_DATA_TYPE[$data_type["type"]]) . (isset($data_type["length"]) ? "(".$data_type["length"].")" : "") . " NOT NULL, ";
    }
    $sql .= " `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `update_date` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP )
      ENGINE = InnoDB AUTO_INCREMENT = 1;";
    if($link->query($sql) === TRUE) {
      echo("<li>Table <b>$model_name</b> created successfully" . '</li>');
      $sql = "";
    } else {
      echo("Error creating table <b>$model_name</b>: "  . $link -> error);
      echo $sql;
      $sql = "";
      echo('<br>');
      $error_flag = TRUE;
    }
  }

  // create USER TABLE
  $sql = "CREATE TABLE IF NOT EXISTS `$dbname`.`user` ( 
    `username` VARCHAR(32) NOT NULL, 
    `password` VARCHAR(60) NOT NULL, 
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `token` VARCHAR(32), 
    `token_expiring_date` DATETIME DEFAULT CURRENT_TIMESTAMP
    )
    ENGINE = InnoDB AUTO_INCREMENT = 1;";
  if($link->query($sql) === TRUE) {
    echo("<li>Table <b>user</b> created successfully" . '</li>');
    $sql = "";
  } else {
    echo("Error creating table <b>user</b>: "  . $link -> error);
    $sql = "";
    echo('<br>');
    $error_flag = TRUE;
  }

  // create RELATIONS
  foreach ($encoded_models['relations'] as $table => $relations) {
    if($relations['hasOne'] != []) {
      foreach ($relations['hasOne'] as $table_to_relate) {
        $sql = "ALTER TABLE `$dbname`.`$table` ADD `". $table_to_relate ."_id` INT";
        if($link->query($sql) === TRUE) {
          echo("Field <b>" . $table_to_relate."_id</b> added");
          echo('<br>');
          $sql = "ALTER TABLE `$dbname`.`$table` ADD FOREIGN KEY (" . $table_to_relate . "_id) REFERENCES $dbname.$table_to_relate(id) ON UPDATE CASCADE ON DELETE SET NULL";
          if($link->query($sql) === TRUE) {
            echo("<li>Relation between <b>$table</b> and <b>$table_to_relate</b> created successfully".'</li>');
            echo('<br>');
          }
          else {
            echo("Error creating foreign key: "  . $link -> error);
            echo('<br>');
          }
        } else {
          echo("Error creating relations between <b>$table</b> and <b>$table_to_relate</b> : "  . $link -> error);
          $sql = "";
          echo('<br>');
          $error_flag = TRUE;
        }
      }
    }
    if($relations['hasMany'] != []) {
      foreach ($relations['hasMany'] as $table_to_relate) {
        $sql = "CREATE TABLE IF NOT EXISTS `$dbname`.`$table". '_' . "$table_to_relate` (
          `$table".'_id`'." INT,
          `$table_to_relate".'_id`'." INT, 
          FOREIGN KEY (" . $table . "_id) REFERENCES $dbname.$table(id) ON UPDATE CASCADE ON DELETE CASCADE, 
          FOREIGN KEY (" . $table_to_relate . "_id) REFERENCES $dbname.$table_to_relate(id) ON UPDATE CASCADE ON DELETE CASCADE
        )
        ENGINE = InnoDB;";
        if($link->query($sql) === TRUE) {
          echo("<li>Table <b>$table _ $table_to_relate</b> created successfully" . '</li>');
          $sql = "";
        } else {
          echo("Error creating table <b>user</b>: "  . $link -> error);
          echo($sql);
          $sql = "";
          echo('<br>');
          $error_flag = TRUE;
        }
        echo($sql);
      }
    }
  }

  // create TRIGGERS (MAYBE ARE NOT NECESSARY)

  // create MAIN USER
  $sql = "INSERT INTO `test`.`user` (`username`, `password`)
          SELECT * FROM (SELECT 'admin', '" . password_hash("admin", PASSWORD_BCRYPT) . "') AS tmp
          WHERE NOT EXISTS (
            SELECT username FROM `test`.`user` WHERE `username` = 'admin'
          ) LIMIT 1;";

  if($link->query($sql) === TRUE) {
    echo("<li><b>Main user</b> created successfully: <li>username: <i>admin</i></li><li>password: <i>admin</i></li>" . '</li>');
    $sql = "";
  } else {
    echo("Error creating <b>user</b>: "  . $link -> error);
    $sql = "";
    echo('<br>');
    $error_flag = TRUE;
  }

  echo('</ul>');
  if($error_flag) {
    echo("<br><br><i><b>An error occurred!</b></i>");
  } else {
    echo("<br><br><i><b>All Done!</b></i>");
  }
  $link -> close();