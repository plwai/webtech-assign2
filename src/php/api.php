<?php
header("Access-Control-Allow-Origin: *");
header("Content-type:text/plain");

require_once __DIR__ . '/database_config.php';
$db_conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

require_once __DIR__ . '/menu_class.php';
$menu = new MENU();

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

          $menu->saveOrder($db_conn, $jsonstr_customer, $jsonstr_order);

        }

        break;

      case 'GET':
        if(isset($_GET["action"]))
          {
            switch($_GET["action"])
            {
              case "view_all_orders": $menu->viewAllOrders($db_conn); break;
              case "view_all_customers": $menu->viewAllCustomers($db_conn); break;
              default: echo "Incorrect action"; break;
            }

          }
         else
          echo('Incorrect GET action');
        
        break;

      case 'DELETE':
        parse_str(file_get_contents("php://input"), $ajax_vars);

        if(isset($ajax_vars["action"]))
        {
          $idOrder = $ajax_vars["idOrder"];
          switch($ajax_vars["action"])
          {
            case "delete_order": $menu->deleteOrder($db_conn, $idOrder); break;
            case "delete_all_orders": $menu->deleteAllOrders($db_conn); break;
            default: echo "Incorrect action"; break;
          }

        }
        else
          echo('Incorrect DELETE action');

        break;

      default:
      echo"Server query method is incorrect";
        break;
    }

}

?>