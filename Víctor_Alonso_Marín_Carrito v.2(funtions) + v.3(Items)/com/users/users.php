<?php
$users_file = 'C:\Xampphtdocs\phpsqlserver\Carrito Aure Acabar sin HTML\xmldb\users.xml';


function UserLogin(){

    
}
/////////////////////////////////////////////////////////////////////////
function UserExistInConnections($username, $connections) {
    foreach ($connections->connection as $connection) {
        if ((string)$connection->user === $username) {
            return true; // El usuario ya está conectado
        }
    }
    return false; // El usuario no está conectado
}
/////////////////////////////////////////////////////////////////////////
function UserRegister($dni, $name) {
    // Cargar la lista de usuarios
    $users = GetUsers();
    
    // Comprobar si ya existe un usuario con el mismo DNI
foreach ($users->user as $user) {
    if ($user->dni == $dni) {
        echo "El usuario con DNI $dni ya existe.";
        return;
    }
    }
}
/////////////////////////////////////////////////////////////////////////
function GetUsers() {
    global $users_file;
    
    if (file_exists($users_file)) {
        // Cargar el archivo XML de usuarios
        $users = simplexml_load_file($users_file);
    } else {
        // Si el archivo no existe, crear un XML vacío
        $users = new SimpleXMLElement('<users></users>');
    }
    
    return $users;
}
/////////////////////////////////////////////////////////////////////////
function DisplayUsers() {
    $users = GetUsers();
    
    // Comprobar si hay usuarios
    if (count($users->user) > 0) {
        echo "<h2>Lista de Usuarios</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Username</th><th>DNI</th></tr>";
        
        foreach ($users->user as $user) {
            echo "<tr>";
            echo "<td>" . $user->id . "</td>";
            echo "<td>" . $user->username . "</td>";
            echo "<td>" . $user->dni . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "No hay usuarios registrados.";
    }
}
?>