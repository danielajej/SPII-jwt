<?php
require_once "vendor/autoload.php";

use Firebase\JWT\JWT;
Use Firebase\JWT\Key;
class Auth
{
    private static $secret_key = 'Sdw1s9x8@';
    private static $encrypt = ['HS256'];
    private static $aud = null;

    public static function SignIn($data){
        $time = time();

        $token = array(
            'exp' => $time + (60*60),
            'aud' => self::Aud(),
            'data' => $data
        );

        return JWT::encode($token, self::$secret_key, 'HS256');
    }

    public static function Check($token){
        if(empty($token))
        {
            throw new Exception("Invalid token supplied.");
        }
        $decode = JWT::decode(
            $token, 
                self::$secret_key,
                self::$encrypt
        );
        $response = [
            'token' => $token,
            'decodedToken' => $decode
        ];

        if($decode->aud !== self::Aud())
        {
            throw new Exception("Invalid user logged in.");
        }
        return $response;
    }

    public static function GetData($token){
        return JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        )->data;
    }

    private static function Aud(){
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
?>