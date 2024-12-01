<?php

// aqui hacer StartConexion y verUltimaConexion     [extra: delogearte (borrar la conexion)]

////////////////////////////////////////////>  CLASE LOGIN   <//////////////////////////////////////////////////
class Login {
////////////////////////////////////////// [ PUBLIC CONSTRUCT ] ///////////////////////////////////////////////

    private $connectionFile;

    public function __construct($connectionsFile = 'xmldb/connection.xml') {
        $this->connectionFile = $connectionsFile;
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////// [  PRIVATE FUNCTIONS  ] ///////////////////////////////////////////

    //== PRIVATE: CARGAR CONEXIONES ==//
    private function loadConnections() {
        if (file_exists($this->connectionFile)) {
            return simplexml_load_file($this->connectionFile);
        } else {
            return new SimpleXMLElement('<connections></connections>');
        }
    }

    //== PRIVATE: GUARDAR CONEXIONES ==//
    private function saveConnections($connections) {
        $connections->asXML($this->connectionFile);
    }

    //== PRIVATE: VERIFICAR SI EL USUARIO ESTÁ CONECTADO ==//  -dice si el user esta conectado(true) o no (false)
    private function isUserConnected($username, $connections) {
        foreach ($connections->connection as $connection) {
            if ($connection->user == $username) {
                $currentTime = time();
                $connectionTime = strtotime($connection->date);
                $expirationTime = $connectionTime + (5 * 60); // 5 minutos

                if ($currentTime < $expirationTime) {
                    return true; // El usuario está conectado
                }
            }
        }
        return false; // El usuario no está conectado
    }



////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////// [  PUBLIC FUNCTIONS  ] ///////////////////////////////////////////


    //== INICIAR SESIÓN: Start Connection ==//
    public function startConnection($username) {
        $connections = $this->loadConnections();

        // Usar isUserConnected para verificar si el usuario ya está conectado
        if ($this->isUserConnected($username, $connections)) { //si el user esta conectado:
            
            foreach ($connections->connection as $connection) { //recorro las conexiones del fichero
                if ($connection->user == $username) {  //si encuentra el usuario:
                    $connection->date = date('Y-m-d H:i:s');   //actualiza la fecha
                    $connection->status = 'conectado';     //cambia el status a conectado
                    break;
                }
            }
        } else {
            // Si el usuario no está conectado, creamos una nueva conexión
            $connection = $connections->addChild('connection');
            $connection->addChild('user', $username);
            $connection->addChild('date', date('Y-m-d H:i:s'));
            $connection->addChild('status', 'conectado');
        }

        // Guardar el archivo actualizado
        $this->saveConnections($connections);

        return "Conexión iniciada o actualizada para el usuario: $username";
    }



    // //== INICIAR SESIÓN: Start Connection ==//
    // public function startConnection($username) {
    //     $connections = $this->loadConnections();
    
    //     // Buscar si el usuario ya tiene una conexión registrada
    //     $userFound = false;
    //     foreach ($connections->connection as $connection) {
    //         if ($connection->user == $username) {
    //             // Si ya existe una conexión, actualizamos la fecha y estado a "conectado"
    //             $connection->date = date('Y-m-d H:i:s');
    //             $connection->status = 'conectado';
    //             $userFound = true;
    //             break;  // Salimos del bucle, ya hemos actualizado la conexión
    //         }
    //     }
    
    //     // Si el usuario no tiene una conexión, agregamos una nueva
    //     if (!$userFound) {
    //         $connection = $connections->addChild('connection');
    //         $connection->addChild('user', $username);
    //         $connection->addChild('date', date('Y-m-d H:i:s'));
    //         $connection->addChild('status', 'conectado');
    //     }
    
    //     // Guardar el archivo actualizado
    //     $this->saveConnections($connections);
    
    //     return "Conexión iniciada o actualizada para el usuario: $username";
    // }
    /////////////////////////////////////////

    //== VER ÚLTIMA CONEXIÓN ==//
    public function getLastConnection($username) {
        $connections = $this->loadConnections();
        
        // Buscar la última conexión del usuario
        foreach ($connections->connection as $connection) {
            if ($connection->user == $username) {
                return "Última conexión de $username: " . $connection->date;
            }
        }
        
        return "Error: No se encontró una conexión previa para el usuario: $username";
    }
    //////////////////////////////


    //== CERRAR SESIÓN: Logout ==//
    public function logout($username) {
        $connections = $this->loadConnections();
        
        // Buscar la conexión del usuario y actualizar el estado a "desconectado"
        foreach ($connections->connection as $connection) {
            if ($connection->user == $username) {
                $connection->status = 'desconectado';  // Actualizar estado
                $connection->date = date('Y-m-d H:i:s');  // Actualizar la fecha al momento de cerrar sesión
                $this->saveConnections($connections);
                return "Usuario $username desconectado.";
            }
        }
    
        return "Error: No se encontró una conexión activa para el usuario: $username";
    }
    //////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////



    
}
?>
