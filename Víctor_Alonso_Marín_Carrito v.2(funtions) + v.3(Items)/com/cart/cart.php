<?php

////////////////////////////////[ AGREGAR PRODUCTO AL CARRITO ]///////////////////////////////
function addToCart($productId, $quantity) {                                    //se define la funcion
    if (checkStock($productId, $quantity)) {                                   //si existe:
        $cart = loadCart();

        foreach ($cart->product_item as $item) {
            if ((int)$item->id_product == $productId) {
                $item->quantity = (int)$item->quantity + $quantity;
                updateStock($productId, $quantity);
                saveCart($cart);
                return "Producto actualizado en el carrito.";
            }
        }

        $newProduct = $cart->addChild('product_item');
        $newProduct->addChild('id_product', $productId);
        $newProduct->addChild('quantity', $quantity);
        $priceItem = $newProduct->addChild('price_item');
        
        $catalog = loadCatalog();
        foreach ($catalog->product as $product) {
            if ((int)$product->id == $productId) {
                $priceItem->addChild('price', $product->price);
                $priceItem->addChild('currency', $product->currency);
            }
        }

        updateStock($productId, $quantity);
        saveCart($cart);

        return "Producto agregado al carrito.";
    } else {
        return "Stock insuficiente o producto no disponible.";
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////[ CARGAR ARCHIVO CART.XML ]//////[ GUARDAR EL CARRITO ACTUALIZADO ]///////[ ACUTALIZAR STOCK DE CATALOGO ]/////////

function loadCart() {
    $filePath = __DIR__ . '/../../xmldb/cart.xml';
    if (file_exists($filePath)) {                         //Si la ruta existe retorna el fichero abierto
        return simplexml_load_file($filePath);   
    } else {
        die("Error: No se pudo encontrar el archivo cart.xml en la ruta: " . $filePath);
    }
}

function saveCart($cart) {
    $cart->asXML(__DIR__ . '/../../xmldb/cart.xml');
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////// [ CALCULAR TOTAL DEL CARRITO ] //////////////////////////

function Calcular_PrecioTotal_Carrito() {
    $cart = loadCart(); // Cargar el carrito
    $total = 0; // Inicializar el total en 0

    
    foreach ($cart->product_item as $item) {  // Recorre cada producto en el carrito
        $quantity = (int)$item->quantity; // Obtiene cantidad de producto
        $price = (float)$item->price_item->price; // Obtiene el precio tambien de ese mismo producto
        $total += $quantity * $price; // Multiplica quantity*price para sumarlo al total
    }

    return $total; // Precio total del carrito
}

////////////////////////// [ MODIFICAR CANTIDAD DE PRODUCTO ] //////////////////////////

function Sumar_CantidadProducto_al_Carrito($productId, $cantidad) {
    $cart = loadCart(); // Cargar el carrito

    // Recorrer cada producto en el carrito
    foreach ($cart->product_item as $item) {
        if ((int)$item->id_product == $productId) {
            // Restar o sumar la cantidad deseada
            $nuevaCantidad = (int)$item->quantity + $cantidad;

            // Si la nueva cantidad es menor a 0, mostrar un mensaje de error
            if ($nuevaCantidad < 0) {
                return "Error: No puedes tener una cantidad negativa con esta funcion.";
            }

            // Asignar la nueva cantidad al producto
            $item->quantity = $nuevaCantidad;

            // Guardar el carrito con los cambios realizados
            saveCart($cart);
            return "Cantidad de producto actualizada exitosamente.";
        }
    }

    return "Error: Producto no encontrado en el carrito.";
}





// Ejemplo de uso
echo "Total del carrito: " . Calcular_PrecioTotal_Carrito() . " EUR"; // Calcula y muestra el total
echo Sumar_CantidadProducto_al_Carrito(5, -5); // Ejemplo para restar 5 del producto con ID 5


?>
