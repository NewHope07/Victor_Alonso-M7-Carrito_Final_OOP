<?php

////////////==== VARIABLES URL ====/////////////
$action = isset($_GET['action']) ? $_GET['action'] : null;
$user = isset($_GET['user']) ? $_GET['user'] : null;
$contraseña = isset($_GET['contraseña']) ? $_GET['contraseña'] : null;
$productId = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : null;
////////////////////////////////////////////////

////////////////////==== TODAS LAS CLASES ====///////////////////
include_once 'com/cart/clsCart.php';
include_once 'com/catalog/clsCatalog.php';
include_once 'com/users/clsLogin.php';
include_once 'com/users/clsUsers.php';
////////////////////////////////////////////////////////////////

////////// ARCHIVOS XML ////////////////
// Si hay user, cargamos su carrito, si no, el default
$cartFile = isset($user) ? "xmldb/carritos/{$user}.xml" : 'xmldb/cart.xml';
$catalogFile = 'xmldb/catalog.xml';
$connectionFile = 'xmldb/connection.xml';
////////////////////////////////////////

////////// CREACION INSTANCIAS ////////////////
$cart = new Cart($cartFile, $catalogFile, $user);   //guardamos en variables las clases (y se ejecutan sus contructores de paso)
$catalog = new Catalog($catalogFile);
$login = new Login($connectionFile);
///////////////////////////////////////////////



///////////////////////////////[ CODIGO CON ACTIONS SEGUN LA URL ]//////////////////////////////////////////////////

// Usar un switch para gestionar la acción
switch ($action) {

//////////////////////////////////////////////////////////////////  >>[CART]
    case 'addToCart':
        // Agregar producto al carrito
        if ($productId !== null && $quantity !== null) {
            echo $cart->addToCart($productId, $quantity);
        } else {
            echo "Error: Se requieren product_id y quantity.";
        }
        break;

    case 'removeFromCart':
        // Restar o eliminar un producto del carrito
        if ($productId !== null && $quantity !== null) {
            echo $cart->removeFromCart($productId, $quantity);
        } else {
            echo "Error: Se requieren product_id y quantity.";
        }
        break;

    case 'clearCart':
        // Eliminar el carrito completo
        echo $cart->clearCart();
        break;

    case 'updateItemQuantity':
        // Actualizar cantidad de un producto en el carrito
        $newQuantity = isset($_GET['new_quantity']) ? $_GET['new_quantity'] : null;
        if ($productId !== null && $newQuantity !== null) {
            echo $cart->updateItemQuantity($productId, $newQuantity);
        } else {
            echo "Error: Se requieren product_id y new_quantity.";
        }
        break;

    case 'nuevoCarrito':
        if ($user !== null) {
            echo $cart->crearNuevoCarrito();
        } else {
            echo "Error: Se requiere un nombre de usuario para crear un nuevo carrito.";
        }
        break;

//////////////////////////////////////////////////////////////////////////////   >>[CATALOG]

    case 'checkStock': // Muestra el ID y el STOCK
        echo $catalog->showAllStock();
        break;

    case 'addProduct':
        // Añadir un nuevo producto al catálogo
        $name = isset($_GET['name']) ? $_GET['name'] : null;
        $price = isset($_GET['price']) ? $_GET['price'] : null;
        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;
        $stock = isset($_GET['stock']) ? $_GET['stock'] : null;

        if ($productId !== null && $name !== null && $price !== null && $currency !== null && $stock !== null) {
            echo $catalog->addProduct($productId, $name, $price, $currency, $stock);
        } else {
            echo "Error: Se requieren id, name, price, currency y stock.";
        }
        break;

    case 'deleteProduct':
        // Eliminar un producto del catálogo
        if ($productId !== null) {
            echo $catalog->deleteProduct($productId);
        } else {
            echo "Error: Se requiere product_id para eliminar el producto.";
        }
        break;

//
    case 'sumarStock':
        echo $catalog->sumarStock($productId, $quantity);
        break;
//

    // case 'sumarStock':
    //     // Sumar stock de un producto
    //     if ($productId !== null && $quantity !== null) {
    //         echo $catalog->sumarStock($productId, $quantity);
    //     } else {
    //         echo "Error: Se requieren product_id y quantity para sumar stock.";
    //     }
    //     break;

    case 'restarStock':
        // Restar stock de un producto
        if ($productId !== null && $quantity !== null) {
            echo $catalog->restarStock($productId, $quantity);
        } else {
            echo "Error: Se requieren product_id y quantity para restar stock.";
        }
        break;

//////////////////////////////////////////////////////////////////////////////    >>[LOGIN]
    case 'startConnection':
        if ($user !== null) {
            echo $login->startConnection($user);
        } else {
            echo "Error: Se requiere un nombre de usuario para iniciar la conexión.";
        }
        break;

    case 'getLastConnection':
        if ($user !== null) {
            echo $login->getLastConnection($user);
        } else {
            echo "Error: Se requiere un nombre de usuario para obtener la última conexión.";
        }
        break;

    case 'logout':
        if ($user !== null) {
            echo $login->logout($user);
        } else {
            echo "Error: Se requiere un nombre de usuario para cerrar sesión.";
        }
        break;


///////////////////////////////////////////////////////////////////////////////////////////
    default:
        echo "Acción no válida o no especificada.";
        break;
}

?>

