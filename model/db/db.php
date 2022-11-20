<?php
namespace db; 
use mysqli;

class db{//Declaramos la clase en php
    static $host ="localhost";
    static $dbname ="sistemaspropietarios";
    static $username ="root";
    static $password ="";

    public static function connect(){ 
        return new mysqli(
            self::$host,
            self::$username,
            self::$password, 
            self::$dbname
        );
    }
    public static function query($query){
        $results = []; 
        $db = self::connect();
        $result = $db->query($query);
        
        if (!is_bool($result)){
            while($row = $result->fetch_object()){ 
                $results[] = $row;
            }
            return $results;
        }else{
            return $result;
        }
        $db->close(); 

    }
    public static function insertGen($response){
        $tabla = $response['tabla'];
        unset($response['tabla']);

        $keys = array_keys($response);
        $values = array_values($response);
    
        $campos = '';
        $valores = '';
    
        for($i = 0; $i < count($keys); $i++){
            if(!$i == 0){
                $campos = $campos.', '.$keys[$i];  
                $valores = $valores.", '".$values[$i]."'"; 
            }else{
                $campos = $campos.' '.$keys[$i];  
                $valores = $valores."'".$values[$i]."'"; 
            }
        }
        
        $query = 'INSERT INTO '.$tabla.' ('.$campos.') VALUES ('.$valores.');';
        $result = self::query($query);
        
        if ($result){
            $responseData['success'] = true;
            $responseData['message'] = 'se inserto el registro';
        }else{
            $responseData['success'] = false;
            $responseData['message'] = 'error';
        }
        return($responseData);
    }
    public static function updateGen($response){
        $tabla = $response['tabla'];
        unset($response['tabla']);

        $id = $response['id'];
        unset($response['id']);

        $keys = array_keys($response);
        $values = array_values($response);
    
        $set = '';
        foreach($values as $index => $value){
           if (empty($value)){
               unset($values[$index]);
               unset($keys[$index]);
           }
        }
        $values = array_values($values);
        $keys = array_values($keys);

        for($i = 0; $i < count($values); $i++){
            if($values[$i]){
                if(!$i == 0){
                    $set = $set.', '.$keys[$i]." = '".$values[$i]."'";  
                }else{
                    $set = $set.' '.$keys[$i]. " = '".$values[$i]."'";  
                }
            }
        }
        
        $query = 'UPDATE '.$tabla.' SET '.$set.' WHERE id ='.$id.';';
        $result =  self::query($query);

        if ($result){
            $responseData['success'] = true;
            $responseData['message'] = 'se actualizo el registro';
            $responseData['id'] = $id;
        }else{
            $responseData['success'] = false;
            $responseData['message'] = 'error';
        }
        return ($responseData);
    }
    public static function deleteGen($response){
        $tabla = $response['tabla'];
        unset($response['tabla']);

        $id = $response['id'];
        unset($response['id']);

        $query = 'DELETE FROM '.$tabla.' WHERE id ='.$id.';';
        $result = self::query($query);

        if ($result){
            $responseData['success'] = true;
            $responseData['message'] = 'se elimino el registro';
            $responseData['id'] = $id;
        }else{
            $responseData['success'] = false;
            $responseData['message'] = 'error';
        }
        return($responseData);
    }
    function begin(){
        if($this->con == true){
          mysqli_query($this->con, "BEGIN");
        }
    }
    function commit(){
        if($this->con == true){
          mysqli_query($this->con, "COMMIT");
        }
      }
      function rollback(){
        if($this->con == true){
          mysqli_query($this->con, "ROLLBACK");
        }
      }
}
