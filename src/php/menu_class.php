<?php
class MENU 
{
    function __construct() {
       
    }

    function saveOrder($db_conn, $jsonstr_customer, $jsonstr_order) {
        $dataCustomer = json_decode($jsonstr_customer, true);
        $dataOrder = json_decode($jsonstr_order, true);

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


    function viewOrders($db_conn){
        // get all orders from orders table
        $sql = "SELECT * FROM orders";
                if (!mysqli_query($db_conn, $sql)) {
                    $error = mysqli_error($db_conn);
                }
                else{
                    $result=mysqli_query($db_conn,$sql);
                }
         
        if (mysqli_num_rows($result) > 0) {
            
            //create an array
            $responseJSON = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $responseJSON[] = $row;
            }

        } else {
            $responseJSON["message"] = "No orders found";
        }

        echo json_encode(array_values($responseJSON), JSON_UNESCAPED_SLASHES);
    }


    function deleteOrder($db_conn, $idOrder){
        // Check connection
        if (mysqli_connect_errno()) {
            $error = mysqli_connect_error();
            echo "{error: \"$error\"}";
            
        } else {

            $error = "";

            $sql = "DELETE FROM `orders` WHERE id_order = $idOrder";
                if (!mysqli_query($db_conn, $sql)) {
                    $error = mysqli_error($db_conn);
                }

            // check if order has been deleted
            if (mysqli_affected_rows($db_conn) > 0) {
                echo "Order successfully deleted";
            } else {
                echo "Order ID ". $idOrder . " not found";
            }
        }  
    }

    function deleteAllOrders($db_conn){
        // Check connection
        if (mysqli_connect_errno()) {
            $error = mysqli_connect_error();
            echo "{error: \"$error\"}";
            
        } else {

            $error = "";
            
            $sql = "DELETE FROM `orders` ";
                if (!mysqli_query($db_conn, $sql)) {
                    $error = mysqli_error($db_conn);
                }

            // check if order has been deleted
            if (mysqli_affected_rows($db_conn) > 0) {
                echo "ALL ORDERS DELETED";
            } else {
                echo "Order table is empty";
            }
        }  
    }
   

}

?>