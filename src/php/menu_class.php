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

                      $sql2 = "insert into `orders` (item, price, qty, id_customer) VALUES('".mysqli_real_escape_string($db_conn,$item)."', '$price', '$quantity', '$customerID')";
                      if (!mysqli_query($db_conn, $sql2)) {
                          $error .= mysqli_error($db_conn);
                      }

              }

              if ($error != "") {
                      echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";

                  } else {
                      echo json_encode("{error: null,customer: $jsonstr_customer, order: $jsonstr_order}");
                  }
          }
    }


    function viewAllOrders($db_conn){
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

        echo json_encode($responseJSON);
    }

    function viewAllCustomers($db_conn){
        // get all orders from orders table
        $sql = "SELECT * FROM customers";
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

        echo json_encode($responseJSON);
    }


    function viewCustomerOrder($db_conn, $id_customer){

        $sql = mysqli_query($db_conn, "SELECT * FROM customers WHERE id_customer = $id_customer");
        $sql2 = mysqli_query($db_conn, "SELECT * FROM orders WHERE id_customer = $id_customer");

        if (!empty($sql)) {
            if (mysqli_num_rows($sql) > 0) {

                $sql = mysqli_fetch_array($sql);
                $data = array("customerInfo" => array(), "orders" => array());
                $customer = array();
                $customer["id_customer"] = $sql["id_customer"];
                $customer["name"] = $sql["name"];
                $customer["address"] = $sql["address"];
                $customer["area"] = $sql["area"];

                array_push($data["customerInfo"], $customer);

                if(!empty($sql2))
                {
                    if (mysqli_num_rows($sql2) > 0){
                        while ($orders = mysqli_fetch_array($sql2)){
                            $order = array();
                            $order["item"] = $orders["item"];
                            $order["price"] = $orders["price"];
                            $order["qty"] = $orders["qty"];
                            array_push($data["orders"], $order);
                            }
                        }
                }

            } else {

                echo "No items found";
            }
        } else {

            echo "Customer " . $id_customer . " not found.";
        }
        echo json_encode($data);
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

    function deleteCustomerOrders($db_conn, $id_customer){
        // Check connection
        if (mysqli_connect_errno()) {
            $error = mysqli_connect_error();
            echo "{error: \"$error\"}";

        } else {

            $error = "";

            $sql1 = "DELETE FROM `customers` WHERE id_customer = $id_customer";
                if (!mysqli_query($db_conn, $sql1)) {
                    $error = mysqli_error($db_conn);
                }

            // check if order has been deleted
            if (mysqli_affected_rows($db_conn) == 0) {
              echo "Customer does not exist";
              return;
            }

            $sql2 = "DELETE FROM `orders` WHERE id_customer = $id_customer";
                if (!mysqli_query($db_conn, $sql2)) {
                    $error = mysqli_error($db_conn);
                }

            // check if order has been deleted
            if (mysqli_affected_rows($db_conn) == 0) {
                echo "Customer does not have any orders";
                return;
            }

            echo json_encode("{success: true}");
        }
    }


}

?>
