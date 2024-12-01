<?php
////////////////////////////////////////////>  CLASE CATALOG   <//////////////////////////////////////////////////
class Catalog {
////////////////////////////////////////// [ PUBLIC CONSTRUCT ] ///////////////////////////////////////////////

    private $catalogFile;

    public function __construct($catalogFile = 'catalog.xml') {
        $this->catalogFile = "{$catalogFile}"; // Ruta XMLcatalogo
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////// [  PRIVATE FUNCTIONS  ] ///////////////////////////////////////////
    
    //== CARGAR CATALOGO XML ==//
    private function loadCatalog() {
        if (file_exists($this->catalogFile)) {
            return simplexml_load_file($this->catalogFile); // Cargar el XML del catálogo
        } else {
            die("Error: No se pudo cargar el catálogo desde: " . $this->catalogFile);
        }
    }

    //== GUARDAR CATALOGO XML ==//
    private function saveCatalog($catalog) {
        $catalog->asXML($this->catalogFile);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////// [  PUBLIC FUNCTIONS  ] ///////////////////////////////////////////

    //== MOSTRAR CATALOGO (STOCK + ID) ==//
    public function showAllStock() {
        $catalog = $this->loadCatalog();
        $response = new SimpleXMLElement('<response/>'); //xml de respuesta
        foreach ($catalog->product as $product) { // Bucle productos catalogo  y añade un hijo con cada iteracion al response
            $productElement = $response->addChild('product');
            $productElement->addChild('id', (int)$product->id);
            $productElement->addChild('stock', (int)$product->stock);
        }
        //Muesra el xml por pantalla
        header('Content-Type: application/xml; charset=utf-8');
        echo $response->asXML();
    }

    //== AÑADIR PRODUCTO AL CATALOGO ==//
    public function addProduct($id, $name, $price, $currency, $stock) {
        $catalog = $this->loadCatalog();
        
        $newProduct = $catalog->addChild('product');
        $newProduct->addChild('id', $id);
        $newProduct->addChild('name', $name);
        $newProduct->addChild('price', $price);
        $newProduct->addChild('currency', $currency);
        $newProduct->addChild('stock', $stock);
        
        $this->saveCatalog($catalog);
 
        // Mostrar el catálogo actualizado por pantalla
        header('Content-Type: application/xml; charset=utf-8');
        echo $catalog->asXML();
    }
    
    //== BORRAR PRODUCTO DEL CATALOGO ==//
    public function deleteProduct($id) {
        $catalog = $this->loadCatalog();
        
        // Encontrar el producto con el ID dado y eliminarlo
        foreach ($catalog->product as $index => $product) {
            if ((int)$product->id == $id) {
                unset($catalog->product[$index]);
                $this->saveCatalog($catalog);

                // Mostrar el catálogo actualizado por pantalla
                header('Content-Type: application/xml; charset=utf-8');
                echo $catalog->asXML();

            }
        }
        
        return "Error: Producto no encontrado en el catálogo.";
    }
    
    //== SUMAR STOCK ==//
    public function sumarStock($productId, $quantity) {
        $catalog = $this->loadCatalog();
        foreach ($catalog->product as $product) {
            if ((int)$product->id == $productId) {
                $product->stock = (int)$product->stock + $quantity;
                $this->saveCatalog($catalog);
                
                // Preparar el tipo de contenido para XML
                header('Content-Type: application/xml; charset=utf-8');
                // Devolver el XML completo
                return $catalog->asXML();
            }
        }
        // Si no encuentra el producto, devuelve el mensaje de error
        return "Error: Producto no encontrado en el catálogo.";
    }

    
    //== RESTAR STOCK ==//
    public function restarStock($productId, $quantity) {
        $catalog = $this->loadCatalog();
        $productFound = false; // Variable para verificar si se encuentra el producto

        foreach ($catalog->product as $product) {
            if ((int)$product->id == $productId) {
                $productFound = true;
                if ((int)$product->stock >= $quantity) {
                    $product->stock = (int)$product->stock - $quantity;
                    $this->saveCatalog($catalog);
                    
                    // Preparar el tipo de contenido para XML
                    header('Content-Type: application/xml; charset=utf-8');
                    return $catalog->asXML(); // Retorna el XML completo
                } else {
                    return "Error: Stock insuficiente.";
                }
            }
        }

        // Si no se encuentra el producto, devolver mensaje de error
        if (!$productFound) {
            return "Error: Producto no encontrado en el catálogo.";
        }
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////












}
?>
