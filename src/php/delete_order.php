<?php
$url_list = array("delete_order", "delete_all_orders");


// Connect and select database.
$db_conn = mysqli_connect("localhost","root","","marrybrowndb");

if (isset($_GET["action"]) && in_array($_GET["action"], $url_list)) {


	// Check connection
	if (mysqli_connect_errno()) {
	    $error = mysqli_connect_error();
	    echo "{error: \"$error\"}";
	    
	} else {
		$idOrder = $_GET['idOrder'];

		switch($_GET["action"])
	    {
	    	case "delete_order":
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
			    break;
			case "delete_all_orders":
				
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
				break;

	    }

	    

	}


    

	
}
?>
