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
        // if (isset($_GET["id_customer"]) && $_GET["action"] == "view_all") 
        {
          $orders_list = curl_get_data('http://localhost/webtech/php/view_all_orders.php');
          echo $orders_list;
        }

        break;
      case 'DELETE':
        parse_str(file_get_contents("php://input"), $ajax_vars);

        if($ajax_vars["action"] == "delete_order")
        {
          echo $ajax_vars["idOrder"];
          curl_get_data('http://localhost/webtech/php/delete_order.php?action=delete_order&idOrder=' . $ajax_vars["idOrder"]);
        }
        else if($ajax_vars["action"] == "delete_all_orders")
        {
          echo $ajax_vars["idOrder"];
          curl_get_data('http://localhost/webtech/php/delete_order.php?action=delete_all_orders&idOrder=' . 'null');
        }
        break;
      default:
      echo"Server query method is incorrect";
        break;
    }

}

function curl_get_data($url)
{
  $curl = curl_init();
  $timeout = 5;
  curl_setopt($curl,CURLOPT_URL,$url);
  curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($curl);
  curl_close($curl);
  return $data;
}


?>