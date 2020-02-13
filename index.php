<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', 'root', 'curso_angular'); //Conexión con la database

//Configuración de las cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


$app->get("/pruebas", function() use($app){ //Prueba N°1 = localhost/cursoAngularBackend/index.php/pruebas
    echo "Hola mundo desde Slim PHP";
});

$app->get("/probando", function() use($app){ //Prueba N°2
    echo "Otro texto desde slim PHP";
});
//LISTAR LOS PRODUCTOS 200
$app->get('/productos', function() use($db, $app){
    //Mostrar de la tabla los productos mas nuevos
    $sql = 'SELECT * FROM productos ORDER BY id DESC;';
    $query = $db->query($sql); 

    $productos = array();
    while($producto = $query->fetch_assoc()){
        $productos[] = $producto;
    }

    $result = array(
        'status' => 'success',
        'code' => 200,
        'data' => $productos
    );

    echo json_encode($result);
});

//DEVOLVER UN SOLO PRODUCTO code = 201
$app->get('/producto/:id', function($id) use($db, $app){
    $sql = 'SELECT * FROM productos WHERE id = '.$id;
    $query = $db->query($sql);
    
    $result = array( //Array result por defecto
        'status' => 'error',
        'code' => 404,
        'message' => 'Producto no disponible'
    );
    if($query->num_rows == 1){ //Si la query devuelve un numero de columna esta buena.
        $producto = $query->fetch_assoc();
        
        $result = array(
            'status' => 'success',
            'code' => 200,
            'data' => $producto
        );
    }

    echo json_encode($result);
});

//ELIMINAR UN PRODUCTO code 202

$app->get('/delete-producto/:id', function($id) use($db, $app){
    $sql = 'DELETE FROM productos WHERE id ='.$id;
    $query = $db->query($sql);

    if($query){
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'El producto se ha eliminado correctamente'
        );
        
    }else{
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El producto NO se ha eliminado correctamente'
        );
    }

    echo json_encode($result);
});

//ACTUALIZAR UN PRODUCTO code 203

$app->post('/update-producto/:id', function($id) use($db, $app){

    $json = $app->request->post('json');
    //Convertimos el JSON a un objeto
    $data = json_decode($json, true);

    $sql =  "UPDATE productos SET ".
            " nombre = '{$data["nombre"]}', ".
            " description = '{$data["description"]}', ";
    //Para mostrar la imagen
    if(isset($data['imagen'])){  
        $sql .= " imagen = '{$data["imagen"]}', ";
    }

    $sql .= " precio = '{$data["precio"]}' WHERE id = {$id} ";
    
    $query = $db->query($sql);

    if($query){
        $result = array (
            'status' => 'success',
            'code' => 200,
            'message' => 'El producto se ha actualizado correctamente!'
        );
    }else{
        $result = array (
            'status' => 'error',
            'code' => 404,
            'message' => 'El producto no se ha actualizado!'
        );

    }

    echo json_encode($result);

});

//SUBIR UNA IMAGEN A UN PRODUCTO 204
$app->post('/upload-file', function() use($db, $app){
    $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'El archivo no ha podido subirse'
    );

    if(isset($_FILES['uploads'])){ //Creamos carpeta UPLOADS para ahi guardar los datos
        $piramideUploader = new PiramideUploader();

        $upload = $piramideUploader->upload('image',"uploads", "uploads", array('image/jpeg','image/png', 'image/gif', 'imagen/jpg'));
        $file = $piramideUploader->getInfoFile();
        $file_name = $file['complete_name'];

        if(isset($upload) && $upload["uploaded"] == false){
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El archivo no ha podido subirse'
            );  
        }else{
            $result = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El archivo ha podido subirse',
                'filename' => $file_name
            );
        }
    }
    
    echo json_encode($result);
});

//GUARDAR PRODUCTOS
$app->post('/productos', function() use($app, $db){
    $json = $app->request->post('json'); //Recogemos la variabla post json que vamos a enviar
    $data = json_decode($json, true); //Decodificamos el json que nos llega
    //El parametro true hace que se convierta en un Array

    if(!isset($data['nombre'])){
        $data['nombre']=null; //Asi siempre tendra nulo en el caso que no exista.
    }

    if(!isset($data['description'])){
        $data['description']=null; //Asi siempre tendra nulo en el caso que no exista.
    }
    if(!isset($data['precio'])){
        $data['precio']=null; //Asi siempre tendra nulo en el caso que no exista.
    }
    if(!isset($data['imagen'])){
        $data['imagen']=null; //Asi siempre tendra nulo en el caso que no exista.
    }

    $query =    "INSERT INTO productos VALUES(NULL,". 
                " '{$data['nombre']}',".
                " '{$data['description']}',".
                " '{$data['precio']}',".
                " '{$data['imagen']}'".     
                ");";                        //De esta manera tenemos el $query para la db.

    // var_dump($json); //Para ver como funciona
    // var_dump($data); 
    // var_dump($query);

    $insert = $db->query($query);

    $result = array (
        'status' => 'error',
        'code' => 404,
        'message' => 'Producto no se ha creado.'
    );

    if($insert){
        $result = array (
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto creado correctamente'
        );
    }

    echo json_encode($result);
});

$app->run();