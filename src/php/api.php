<?php
header("Access-Control-Allow-Origin: *");
header("Content-type:text/plain");

require_once __DIR__ . '/database_config.php';

$db_conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);


// Check connection
if (mysqli_connect_errno()) {
    $error = mysqli_connect_error();
    echo "{error: \"$error\"}";
    
} else {

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
      case 'POST':

        if($_POST["action"] == "save_order")
        {
          $jsonstr_customer = $_REQUEST["customer"];
          $jsonstr_order = $_REQUEST["order"];

          $dataCustomer = json_decode($jsonstr_customer, true);
          $dataOrder = json_decode($jsonstr_order, true);


          // Check connection
          if (mysqli_connect_errno()) {
              $error = mysqli_connect_error();
              echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";
              
          } else {

              $error = "";

              $name = ($dataCustomer["name"]);
              $address = ($dataCustomer["address"]);
              $area = ($dataCustomer["area"]);    

              $sql = "insert into `customers`(name, address, area) VALUES('$name', '$address', '$area')";
                  if (!mysqli_query($db_conn, $sql)) {
                      $error = mysqli_error($db_conn);
                  }


              $customerID = 0;
              $query = (
                      "select `customers`.`id_customer`
                      FROM `customers`
                      ORDER BY `customers`.`id_customer` DESC LIMIT 1");

              $result = mysqli_query($db_conn, $query);
              $row = mysqli_fetch_assoc($result);
              $customerID = $row['id_customer'];


              foreach($dataOrder as $key => $value) {
                  $item = $key;
                  $count = 0;
                  foreach ($value as $key2 => $value2) {
                          if($count == 0)
                              $price = $value2;
                          else if($count == 1)
                              $quantity = $value2;

                          $count++;
                      }
                  $error = "";
                      
                      $sql2 = "insert into `orders` (item, price, qty, id_customer) VALUES('$item', '$price', '$quantity', '$customerID')";
                      if (!mysqli_query($db_conn, $sql2)) {
                          $error .= mysqli_error($db_conn);
                      }   
                      
              }

              if ($error != "") {
                      echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";
                      
                  } else {
                      echo "{error: null, customer: $jsonstr_customer, order: $jsonstr_order}";
                  }
          }

        }
        

        break;
      case 'GET':
        include ('view_all_orders.php');
        break;
      case 'DELETE':
        parse_str(file_get_contents("php://input"), $ajax_vars);

        if(isset($ajax_vars["action"]))
        {
          include ('delete_order.php');
        }
        else
          echo('Incorrect deletion');

        break;
      default:
      echo"Server query method is incorrect";
        break;
    }

}

?>