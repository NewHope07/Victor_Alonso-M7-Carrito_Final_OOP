<?php
///////////////////////////////////[ CARGAR ARCHIVO CATALOGO.XML ]///////////////////////////////////////////
function loadCatalog() {
    $filePath = __DIR__ . '/../../xmldb/catalog.xml'; // Ruta ajustada
    if (file_exists($filePath)) {
        return simplexml_load_file($filePath);
    } else {
        die("Error: No se pudo encontrar el archivo catalog.xml en la ruta: " . $filePath);
    }
}
//////////////////////////////////////[ FUNCION VERIFICAR STOCK ]/////////////////////////////////////////////
function checkStock($productId, $quantity) {
    $catalog = loadCatalog();

    foreach ($catalog->product as $product) {
        if ((int)$product->id == $productId) {
            return (int)$product->stock >= $quantity;
        }
    }
    return false;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function updateStock($productId, $quantity) {
    $catalog = loadCatalog();

    foreach ($catalog->product as $product) {                           //Guarda en $product los productos del catalogo
        if ((int)$product->id == $productId) {                          //IF el ID de producto es igual al que le hemos dicho:
            $newStock = (int)$product->stock - $quantity;              //Crea variable "NewStock" con el stock restado de lo que nos llevamos a nuestro carrito.
            $product->stock = $newStock;                               //Cambia el stock al nuevo actualizado
            $catalog->asXML(__DIR__ . '/../../xmldb/catalog.xml');
            return true;
        }
    }
    return false;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CALCULAR EL TOTAL DEL CARRITO DEL USUARIO
function calculateCartTotal($username) {
    // RUTA DEL CARRITO DEL USUARIO
    $userCartFile = "xmldb/carritos/{$username}.xml";
    
    // CARGAR EL CARRITO DEL USUARIO
    if (!file_exists($userCartFile)) {
        return "El carrito del usuario {$username} no existe.";
    }

    $cart = simplexml_load_file($userCartFile);
    $catalog = loadCatalog(); // CARGAR CATALOGO
    $total = 0;

    // RECORRER LOS PRODUCTOS EN EL CARRITO
    foreach ($cart->product as $cartProduct) {
        $productId = (int)$cartProduct->id;
        $quantity = (int)$cartProduct->quantity;

        // BUSCAR EL PRODUCTO EN EL CATALOGO
        foreach ($catalog->product as $catalogProduct) {
            if ((int)$catalogProduct->id === $productId) {
                $price = (float)$catalogProduct->price; // SUPONIENDO QUE HAY UN CAMPO DE PRECIO
                $total += $price * $quantity; // CALCULAR TOTAL
                break; // SALIR DEL BUCLE CUANDO ENCONTRAMOS EL PRODUCTO
            }
        }
    }

    return $total; // RETORNAR EL TOTAL DEL CARRITO
}

//eliminar_del_catalogo

?>