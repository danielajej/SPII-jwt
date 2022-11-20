<?php
    header("Access-Control-Allow-Origin: *"); 
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
    header("Content-Type: application/json");

    require_once './model/db/db.php';
    require_once './model/Auth.php';
    use db\db;

    $requestData = json_decode(file_get_contents('php://input'));
    $_exec_funcion = $requestData->_function;

    if (function_exists($_exec_funcion)) {
        call_user_func($_exec_funcion, $requestData);
    } else {
        $_string = "No existe la funcion : " . $_exec_funcion;
        $_data_error = array("error" => true, "message" => $_string);
        echo json_encode($_data_error);
    }
    function register($requestData){
        //insert data
        $data['nombre'] = $requestData->name;
        $data['apellido_paterno'] = $requestData->lastname1;
        $data['apellido_materno'] = $requestData->lastname2;
        $data['email'] = $requestData->email;
        $data['activo'] = 1;
        $rolList = $requestData->rol;
        //password hashing
        $password = $requestData->password;
        $data['contrasena'] = password_hash($password, PASSWORD_DEFAULT, ['cost'=>10]);
        $data['tabla'] = 'tblusuario';
        //perform insert
        $result = db::insertGen($data);
        if($result['success'] == true){
            //obtenemos los ids de los perfiles seleccionados
            $sql = "SELECT * FROM tblperfil;";
            $rolResult = db::query($sql);
            $rolIds = [];
            echo json_encode($rolList);
            echo json_encode($rolResult);

            foreach($rolResult as $rolRow){
                if(count($rolList)>1){
                    foreach($rolList as $rol){
                        if($rol == $rolRow->nombre){
                            $rolIds[] = $rolRow->id;
                        }
                    }
                }else{
                    if($rolRow->nombre == $rolList){
                        $rolIds[] = $rolRow->id;
                    }
                }
                
            }
            //obtenemos el id del usuario recién registrado
            $sql = "SELECT id FROM tblusuario WHERE email = '$requestData->email';";
            $id = db::query($sql)[0]->id;
            //hacemos los inserts para cada perfil seleccionado
            foreach($rolIds as $rolId){
                $data = [];
                $data['idUsuario'] = $id;
                $data['idPerfil'] = $rolId;
                $data['tabla'] = 'tblperfilusuario';
                $result = db::insertGen($data);
                if($result['success'] == false){
                    echo json_encode($result);
                    exit;
                }
            }
            echo json_encode($result);
            }else{
            echo json_encode($result);
        }
    }
    function login($requestData){
        $email = $requestData->email;
        $password = $requestData->password;
        //obtener la contraseña del usuario con el email
        $sql = "SELECT id, contrasena FROM tblusuario WHERE email = '$email'";
        $hash = db::query($sql) ? db::query($sql)[0]->contrasena : '';
        $id = db::query($sql) ? db::query($sql)[0]->id : '';
        //si el usuario existe
        if ($id){
            //si la contraseña es correcta
            if (password_verify($password, $hash)) {
                $sql = "SELECT concat(U.nombre, ' ', U.apellido_paterno, ' ', U.apellido_materno)AS usuario, 
                    U.email AS email, P.nombre AS perfil, M.nombre AS modulo,
                    M.nombre AS modulo, M.url AS url
                    FROM `tblperfilusuario` AS PU 
                    JOIN tblperfilmodulos AS PM ON PM.idPerfil = PU.idPerfil 
                    JOIN tblmodulos AS M ON M.id = PM.idModulo 
                    JOIN tblperfil AS P ON P.id = PM.idPerfil 
                    JOIN tblusuario AS U ON U.id = PU.idUsuario 
                    WHERE U.id = $id
                    ORDER BY `PU`.`idUsuario` ASC;";
                //obtenemos los perfiles y los modulos accesibles
                $result = db::query($sql);
                $perfiles = [];
                $modulos = [];
                $modulosUrls = [];
                foreach($result as $row){
                    $perfiles[]=$row->perfil;
                    $modulos[]=$row->modulo;
                    $modulosUrls[]=$row->url;
                }
                $perfiles = array_values(array_unique($perfiles));
                $modulos = array_values(array_unique($modulos));
                $modulosUrls = array_values(array_unique($modulosUrls));
                
                $tokenData['id'] = $id;
                $tokenData['usuario'] = $result[0]->usuario;
                $tokenData['email'] = $result[0]->email;
                $tokenData['perfiles'] = $perfiles;
                $tokenData['modulos'] = $modulos;
                $tokenData['modulosUrls'] = $modulosUrls;
                $_token = Auth::SignIn($tokenData);
                $response['login'] = true;
                $response['token'] = $_token;
                $response['homepage'] = $modulosUrls[0];
            } else {
                $response['login'] = false;
                $response['message'] = 'Usuario o contraseña erróneos';
            }
        }else{
            $response['login'] = false;
            $response['message'] = 'No existe el usuario';
        }
       echo json_encode($response);
    }
    function currentToken(){
        $headers = apache_request_headers();
        $_token = $headers['authorization'];
        try{
            Auth::Check($_token);
            $data_token = (Auth::GetData($_token));
            $data_token->success = true;
            echo json_encode($data_token);
        }
        catch(Exception $e){
            http_response_code(401);
            echo json_encode(array(
                "message" => "acceso denegado",
                "error" => $e->getMessage(),
                "success"=> false
            ));
        }
    }
?>