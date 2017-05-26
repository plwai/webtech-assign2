<?php
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
?>
