<?php
    header("Access-Control-Allow-Origin: *"); 
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
    header("Content-Type: application/json");

    require_once './model/db/db.php';
    require_once './model/Auth.php';
    use db\db;

    $requestData = json_decode(file_get_contents('php://input'));
    $_method = $_SERVER["REQUEST_METHOD"];

    $headers = apache_request_headers();
    $_token = $headers['authorization'];

    try{
        //truena si el token no es válido
        Auth::Check($_token);
        $data_token = (Auth::GetData($_token));
        $data_token->success = true;
        //ejecutamos la funcion del crud
        switch($_method){
            case 'GET':
                $query = "SELECT * FROM tbl_productos WHERE eliminado = 0;";
                $result = db::query($query); 
                echo json_encode($result);
            break;
            case 'POST':
                $query = "INSERT INTO tbl_productos (nombre, precio) 
                VALUES ('$requestData->nombre', $requestData->precio);";
                $result = db::query($query); 
                echo json_encode($result);
            break;
            case 'PUT':
                $query = "UPDATE tbl_productos SET nombre = '$requestData->nombre', precio = $requestData->precio WHERE id = $requestData->id;";
                $result = db::query($query); 
                echo json_encode($result);
            break;
            case 'DELETE':
                $query = "UPDATE tbl_productos SET eliminado = 1 WHERE id = $requestData->id;";
                $result = db::query($query); 
                echo json_encode($result);
            break;
        }
    }
    catch(Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "acceso denegado, acción invalidada",
            "error" => $e->getMessage(),
            "success"=> false
        ));
    }
?>