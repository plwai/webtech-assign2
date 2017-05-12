<?php
header("Access-Control-Allow-Origin: *");
header("Content-type:text/plain");

// Construct PHP's "customer" and "order" object
// instances from JSON string
$jsonstr_customer = $_REQUEST["customer"];
$jsonstr_order = $_REQUEST["order"];
// ???

$dataCustomer = json_decode($jsonstr_customer, true);
$dataOrder = json_decode($jsonstr_order, true);


$count = 0;
// Connect and select database.
$db_conn = mysqli_connect("localhost","root","","marrybrowndb");

// Check connection
if (mysqli_connect_errno()) {
    $error = mysqli_connect_error();
    echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";
    
} else {

    $error = "";

    $name = ($dataCustomer["name"][0]);
    $address = ($dataCustomer["address"][0]);
    $area = ($dataCustomer["area"][0]);    

    $sql = "insert into `customers`(name, address, area) VALUES('$name', '$address', '$area')";
        if (!mysqli_query($db_conn, $sql)) {
            $error = mysqli_error($db_conn);
        }

    foreach($dataOrder as $jsonItem) {
    
        

        $item = ($dataOrder["item".($count)][0]);
        $price = ($dataOrder["item".($count)][1]);
        $quantity = ($dataOrder["item".($count)][2]);
        $count++;

        $error = "";
        
        $sql2 = "insert into `orders` (item, price, quantity) VALUES('$item', '$price', '$quantity')";
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
?>
