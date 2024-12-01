<?php

///////////////////////////////////////////////>  CLASE CART   </////////////////////////////////////////////////////
class Cart {
////////////////////////////////////////// [ PUBLIC CONSTRUCT ] ///////////////////////////////////////////////
    private $cartFile;
    private $catalogFile;

    public function __construct($cartFile = 'cart.xml', $catalogFile = 'catalog.xml', $user) {  //si no se pasa valor de user en la url sera null

      $this->catalogFile = "{$catalogFile}";

      if ($user) { //si se ha encontrado user y no es null:    
          $this->cartFile = "xmldb/carritos/{$user}.xml";  // CARRITO USER
      } else {
          $this->cartFile = "../xmldb/{$cartFile}";  // Si no hay user: CARRITO DEFAULT (cart.xml)
      }

    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////// [  PRIVATE FUNCTIONS  ] ///////////////////////////////////////////

    //== VERIFICAR STOCK CATALOGO ==//                                         -- checkStock
    // Método para verificar el stock de un producto en el catálogo
    private function checkStock($productId, $quantity) {
        $catalog = $this->loadCatalog();
        foreach ($catalog->product as $product) {
            if ((int)$product->id == $productId) {
                return (int)$product->stock >= $quantity;
            }
        }
        return false;
    }
   ///////////////////////////////////
  
  
    //== CARGAR CARRITO XML ==//                                                    -- loadCart
    private function loadCart() {
        if (file_exists($this->cartFile)) {
            return simplexml_load_file($this->cartFile);
        } else {
            die("Error: No se pudo encontrar el archivo cart.xml en la ruta: " . $this->cartFile);
        }
    }
    //////////////////////////////
  
  
  
    //== CARGAR CATALOGO XML ==//                                                 -- loadCatalog
    private function loadCatalog() {
        if (file_exists($this->catalogFile)) {
            return simplexml_load_file($this->catalogFile);
        } else {
            die("Error: No se pudo encontrar el archivo catalog.xml en la ruta: " . $this->catalogFile);
        }
    }
    //////////////////////////////
  
  
    //== ACTUALIZAR STOCK EN CATALOGO XML ==//                                      --updateStock
    private function updateStock($productId, $quantity) {
        $catalog = $this->loadCatalog();
        foreach ($catalog->product as $product) {
            if ((int)$product->id == $productId) {
                $product->stock = (int)$product->stock - $quantity;
            }
        }
        $this->saveCatalog($catalog);
    }
    ///////////////////////////////////////////
  


    //== ACTUALIZAR CARRITO ==//                                                         -- saveCart
    // Método para guardar el carrito actualizado en el archivo XML
    private function saveCart($cart) {
        $cart->asXML($this->cartFile);
    }
  
    // Método para guardar el catálogo actualizado en el archivo XML
    private function saveCatalog($catalog) {
        $catalog->asXML($this->catalogFile);
    }
    //////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////




//////////////////////////////////////// [  PUBLIC FUNCTIONS  ] ///////////////////////////////////////////

    //== AGREGAR PRODUCTO AL CARRITO ==//                                 -- addToCart
   public function addToCart($productId, $quantity) {
    if ($productId === null || $quantity === null) {    // esto es un OR
      return "Error: El product_id y quantity son requeridos.";
    } else {
        if ($this->checkStock($productId, $quantity)) {
            $cart = $this->loadCart();

            // Si el producto ya está en el carrito, actualizamos la cantidad
            foreach ($cart->product_item as $item) {
                if ((int)$item->id_product == $productId) {
                    $item->quantity = (int)$item->quantity + $quantity;
                    $this->updateStock($productId, $quantity);
                    $this->saveCart($cart);
                    return "Producto actualizado en el carrito.";
                }
            }

            // Si el producto no está en el carrito, lo agregamos
            $newProduct = $cart->addChild('product_item');
            $newProduct->addChild('id_product', $productId);
            $newProduct->addChild('quantity', $quantity);

            // Añadimos el precio y la moneda desde el catalogo
            $priceItem = $newProduct->addChild('price_item');
            $catalog = $this->loadCatalog();
            foreach ($catalog->product as $product) {
                if ((int)$product->id == $productId) {
                    $priceItem->addChild('price', $product->price);
                    $priceItem->addChild('currency', $product->currency);
                }
            }

            // Actualizamos el stock y guardamos el carrito
            $this->updateStock($productId, $quantity);
            $this->saveCart($cart);

            return "Producto agregado al carrito.";
        } else {
            return "Stock insuficiente o producto no disponible.";
        }
    }
}


/////////////////////////////////////////////////

//== ELIMINAR PRODUCTOS DEL CARRITO ==//                              -- removeFromCart
public function removeFromCart($productId, $quantity) {
    if ($productId === null || $quantity === null) {
        return "Error: El product_id y quantity son requeridos.";
    } else {
        $cart = $this->loadCart();
        
        // Verificar si el producto está en el carrito
        foreach ($cart->product_item as $item) {
            if ((int)$item->id_product == $productId) {
                $currentQuantity = (int)$item->quantity;
                
                // Si la cantidad en el carrito es mayor a la cantidad solicitada
                if ($currentQuantity > $quantity) {
                    $item->quantity = $currentQuantity - $quantity;
                    $this->updateStock($productId, -$quantity); // Sumar al stock
                    $this->saveCart($cart);
                    return "Producto actualizado en el carrito.";
                } elseif ($currentQuantity === $quantity) {
                    // Si la cantidad en el carrito es igual a la solicitada, eliminar el producto
                    unset($item[0]);
                    $this->updateStock($productId, -$quantity); // Sumar al stock
                    $this->saveCart($cart);
                    return "Producto eliminado del carrito.";
                } else {
                    return "Error: La cantidad en el carrito es menor que la cantidad solicitada.";
                }
            }
        }
        return "Error: El producto no se encuentra en el carrito.";
    }
}


/////////////////////////////////////////////////

    //== ELIMINAR CARRITO COMPLETO ==//
    public function clearCart() {
        if (file_exists($this->cartFile)) {
            unlink($this->cartFile);
            return "Carrito eliminado correctamente.";
        } else {
            return "Error: No se pudo encontrar el archivo del carrito.";
        }
    }

//////////////////////////////////////

    //== MODIFICAR CANTIDAD DE PRODUCTOS EN EL CARRITO ==//
    public function updateItemQuantity($productId, $newQuantity) {
        if ($productId === null || $newQuantity === null) {
            return "Error: El product_id y newQuantity son requeridos.";
        } else {
            $cart = $this->loadCart();

            foreach ($cart->product_item as $item) {
                if ((int)$item->id_product == $productId) {
                    $difference = $newQuantity - (int)$item->quantity;
                    if ($this->checkStock($productId, $difference)) {
                        $item->quantity = $newQuantity;
                        $this->updateStock($productId, $difference);
                        $this->saveCart($cart);
                        return "Cantidad actualizada correctamente en el carrito.";
                    } else {
                        return "Error: No hay suficiente stock para actualizar la cantidad.";
                    }
                }
            }

            return "Error: Producto no encontrado en el carrito.";
        }
    }
    
///////////////////////////////////////
    //== CREAR NUEVO CARRITO VACIO ==//

    public function crearNuevoCarrito() {
        // Verificar si el archivo del carrito ya existe
        if (!file_exists($this->cartFile)) {
            // Crear la estructura XML inicial del carrito
            $cart = new SimpleXMLElement('<cart></cart>');

            // Guardar el carrito en el archivo especificado
            $this->saveCart($cart); // Utiliza el método saveCart para guardar el XML

            return "Nuevo carrito creado exitosamente en {$this->cartFile}.";
        } else {
            return "El carrito ya existe en {$this->cartFile}.";
        }
    }
///////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////////////////////////////////


}
?>
