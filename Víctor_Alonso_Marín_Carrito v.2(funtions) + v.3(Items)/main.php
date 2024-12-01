<?php

//////////[INCLUIR LOS ARCHIVOS CARGADOS]/////////////
include_once("com/cart/cart.php");
include_once("com/catalog/catalog.php");
include_once("com/users/users.php");
include_once("com/users/login.php");
///////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
function loginAndAddToCart($username, $password, $productId, $quantity) {
    // CARGAR ARCHIVO USUARIOS
    $users = simplexml_load_file('xmldb/users.xml');
    $authenticated = false;

    // AUTENTIFICA EL USUARIO
    foreach ($users->user as $user) {
        if ($user->username == $username && $user->password == $password) {
            $authenticated = true;
            break; // USUARIO AUTENTICADO CORRECTAMENTE
        }
    }

    // Mensaje de depuración para verificar la autenticación
    if (!$authenticated) {
        echo "Usuario o contraseña incorrectos para: {$username}.<br>";
        return "CREDENCIALES INCORRECTAS. NO SE PUEDE AGREGAR EL PRODUCTO.";
    } else {
        echo "Usuario {$username} autenticado correctamente.<br>";
    }

    // VERIFICA SI ESTA CONECTADO ULTIMOS 5 MINUTOS
    $connections = simplexml_load_file('connection.xml');
    if (!isUserConnected($username, $connections)) { // SI NO ESTA CONECTADO SE CONECTA
        writeConnection($username);
        echo "Conexión registrada para {$username}.<br>";
    } else {
        echo "{$username} ya está conectado.<br>";
    }

    // RUTA DEL CARRITO DEL USUARIO
    $userCartFile = "xmldb/carritos/{$username}.xml";

    // SI EXISTE EL ARCHIVO XML DEL CARRITO DEL USUARIO
    if (file_exists($userCartFile)) {
        // CARGAR EL CARRITO DEL USUARIO
        $cart = simplexml_load_file($userCartFile);
    } else {
        // CREAR UN NUEVO CARRITO XML PARA EL USUARIO
        $cart = new SimpleXMLElement('<cart></cart>');
        echo "Carrito de [ {$username} ] creado.<br>";
    }

    // VERIFICAR SI EL PRODUCTO YA EXISTE EN EL CARRITO
    $productExists = false;
    foreach ($cart->product as $product) {
        if ((int)$product->id === (int)$productId) {
            $product->quantity += (int)$quantity; // ACTUALIZA LA CANTIDAD
            $productExists = true;
            echo "Cantidad actualizada en el carrito >> {$productId}.<br>";
            break;
        }
    }

    // SI EL PRODUCTO NO EXISTE, SE AGREGA NUEVO
    if (!$productExists) {
        $product = $cart->addChild('product');
        $product->addChild('id', (int)$productId);
        $product->addChild('quantity', (int)$quantity);
        echo "Producto {$productId} agregado al carrito.<br>";
    }

    // GUARDAR EL CARRITO ACTUALIZADO DEL USUARIO EN SU ARCHIVO
    $cart->asXML($userCartFile);
    return "Productos agregados a su carrito correctamente ✅ ({$username})";
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////[ AGREGAR AL CARRITO DE USER CON LOGIN ]////////////////////////////////////////////////////////

if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $_GET['username'];
    $password = $_GET['password'];
    
    if (isset($_GET['id_product']) && isset($_GET['quantity'])) {
        $productId = (int)$_GET['id_product'];
        $quantity = (int)$_GET['quantity'];
        
        // LLAMAR A LA FUNCION PARA INICIAR SESION Y AGREGAR EL PRODUCTO AL CARRITO
        $result = loginAndAddToCart($username, $password, $productId, $quantity);
        echo $result;
        // CALCULAR Y MOSTRAR EL TOTAL DEL CARRITO
        $total = calculateCartTotal($username);
        echo "<br>Total del carrito de [{$username}]: {$total}";
    } else {
        echo "POR FAVOR, PROPORCIONA UN id_product Y quantity VÁLIDOS EN LA URL.";
    }
} else {  //ERROR CONTROLADO SI NO ESTA EL USUARIO Y CONTRASEÑA EN LA URL
    echo "SE REQUIERE USUARIO Y CONTRASEÑA EN LA URL.";
    exit();
}

//////////////////////////////////////////////////////////////////////////////////////////

//funcion mostrar carrito

// AGREGAMOS EJEMPLO DE URL PARA QUE EL USUARIO LO VEA
echo "<br><br>Ejemplo de uso: <br>";
echo "http://localhost:40080/Carrito%20Aure%20Acabar%20sin%20HTML/Carrito%20v.2/main.php?username=juan123&password=abc123&id_product=1&quantity=2";
?>
