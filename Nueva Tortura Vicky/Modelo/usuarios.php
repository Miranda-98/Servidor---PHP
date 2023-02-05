<?php
require 'conexion.php';

class Usuario extends Conexion
{
    private $conexion;
    public static $TABLA = 'usuarios';

    function __construct($conexion)
    {
        parent::__construct($conexion);
        $this->conexion = parent::conectar();
    }

    function randomPass()
    {
        $x = 12;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        $contraseña = '';
        for ($i = 0; $i < $x; $i++) {
            $contraseña .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $contraseña;
    }


    function crearUsuario($nombre)
    {
        try {
            // almacenar usuario - contraseña antes del cifrado
            $archivo = fopen("contraseñas.txt", "a+b");
            $contraseñaAleatoria = self::randomPass();
            if (!$archivo) {
                echo "error al abrir el fichero";
            } else {
                $escribe = "añadir nuevo usuario -> nombre: " . $nombre . " - contraseña: " . '1234' . " \n ";
                echo self::randomPass();
                fwrite($archivo, $escribe);
                rewind($archivo);
            }

            fclose($archivo);

            //$contraseñaAleatoria = self::randomPass();
            echo $contraseñaAleatoria;
            $contraseñaAleatoria = password_hash('1234', PASSWORD_DEFAULT);
            $cone = $this->conexion;
            $sql = "INSERT INTO " . self::$TABLA . "(id_usuario, password) VALUES (:A, :B)";
            $stmt = $cone->prepare($sql);
            $stmt->bindParam(':A', $nombre);
            $stmt->bindParam(':B', $contraseñaAleatoria);
            $stmt->execute();
            echo '<br/>insertado';
        } catch (PDOException $e) {
            echo "<br/>ERROR AL CREAR USUARIO " . $e->getMessage();
        }
    }

    function eliminarUsuario($nombre)
    {
        try {
            $cone = $this->conexion;
            $sql = "DELETE FROM " . self::$TABLA . " WHERE id_usuario = :A";
            $stmt = $cone->prepare($sql);
            $stmt->bindParam(':A', $nombre);
            $stmt->execute();
            echo "<br/>eliminado";
        } catch (PDOException $e) {
            echo "<br/>ERROR AL ELIMINAR USUARIO " . $e->getMessage();
        }
    }

    function controlUsuarios($id_login, $pass_login)
    {
        try {
            echo "<br/>id login " . $id_login;
            echo "<br/>pass login " . $pass_login;
            $cone = $this->conexion;
            $sql = "SELECT password as 'pepe' FROM usuarios WHERE id_usuario = '" . $id_login . "'";
            $resultado = $cone->query($sql);
            $num = $resultado->fetch();
            
            echo $pass_login . "<br>";
            print_r ($num['pepe']);
            if( password_verify( $pass_login, $num['pepe'])  ){
                echo "true";
                return true;
            }
            
            else{
                echo "false";
                return false;
            }
                
        } catch (PDOException $e) {
            echo "<br/>ERROR AL COMPROBAR EL USUARIO";
        }
    }
}

$user01 = new Usuario('inmobiliaria');
    // $user01->crearUsuario('miranda2');
    // $user01->crearUsuario('miranda3');
    // $user01->crearUsuario('miranda4');
     $user01->crearUsuario('admin');
    // $user01->eliminarUsuario('pepe');
