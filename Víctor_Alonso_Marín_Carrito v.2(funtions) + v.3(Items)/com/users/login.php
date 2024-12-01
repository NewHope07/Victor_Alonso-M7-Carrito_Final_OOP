<?php

////////////////////////////////////////////
// FUNCION QUE MIRA SI ESTA CONECTADO EL USUARIO
function isUserConnected($username, $connections)
{
    foreach ($connections->connection as $connection) {
        if ($connection->user == $username) {
            // Revisa que el usuario esta conectado en los ultimos 5 mintuos.
            $currentTime = time();
            $connectionTime = strtotime($connection->date);
            $expirationTime = $connectionTime + (5 * 60);
            if ($currentTime < $expirationTime) {
                return true; // True = Si esta conectado
            }
        }
    }
    return false; // Falso = No esta conectado
}

// AGREGA LA CONEXION DEL USUARIO AL XML CONEXIONES
function writeConnection($username)
{
    // Load existing connections or create a new XML document
    if (file_exists('connection.xml')) {
        $connections = simplexml_load_file('connection.xml');
    } else {

        $connections = new SimpleXMLElement('<connections></connections>');
    }
    // Create a new connection entry
    $connection = $connections->addChild('connection');
    $connection->addChild('user', $username);
    $connection->addChild('date', date('Y-m-d H:i:s'));
    // Save the updated connections to connection.xml
    $connections->asXML('connection.xml');
}
///////////////////////////////////////////////

//
