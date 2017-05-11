<?php
header("Access-Control-Allow-Origin: *");
header("Content-type:text/plain");

// Construct PHP's "customer" and "order" object
// instances from JSON string
$jsonstr_customer = $_REQUEST["customer"];
$jsonstr_order = $_REQUEST["order"];
// ???

// Connect and select database.
$db_conn = mysqli_connect("localhost","login?","password?","database?");

// Check connection
if (mysqli_connect_errno()) {
    $error = mysqli_connect_error();
    echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";
    
} else {
    // Try to save customer order information into the database.
    // ???
    $error = "";
    
    $sql = "insert into `customer` ...";
    if (!mysqli_query($db_conn, $sql)) {
        $error = mysqli_error($db_conn);
    }
    
    $sql = "insert into `order` ...";
    if (!mysqli_query($db_conn, $sql)) {
        $error .= mysqli_error($db_conn);
    }    
    
    if ($error != "") {
        echo "{error: \"$error\", customer: $jsonstr_customer, order: $jsonstr_order}";
        
    } else {
        echo "{error: null, customer: $jsonstr_customer, order: $jsonstr_order}";
    }
}
?>
