<?php
/**
 * API : 
 * Returns JSON reponse 
 * urls :
 * /api.php           // GET : show all items
 * /api.php           // POST : create item
 * /api.php?id={int}  // GET : get item by id
 * /api.php&id={int}  // PUT : edit item
 * /api.php&id={int}  // DELETE : delete item
 * 
 */



// Traitement des données GET / POST
if (!empty($_GET)) {
    if(!empty($_GET['id']) && is_numeric($_GET['id']) ) {
        find($_GET['id']);
    }
}
elseif (!empty($_POST)) {
    if(!empty($_POST['id']) && is_numeric($_POST['id']) ) {
        find($_POST['id']);
    }    
}
else {
    findAll(); // default output
}



    function dump($data) {
        echo '<pre>';
        print_r($data);
        json_decode($data);
        echo '</pre>';
    }

    function render($data) {
       echo json_encode($data);
    }
    
    function db_query(string $query, ?array $params = null) {

        // require_once('./config/database.php');

        $db['user'] = 'root';
        $db['password'] = '';
        $db['host'] = 'localhost';
        $db['base'] = 'site';
        $db['port'] = '3306'; // a confirmer

        try{
            $database = new PDO('mysql:host=localhost;dbname=site;charset=utf8', 'root', '');            
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if(!empty($params)) {
                $req = $database->prepare($query);
                $req->execute($params);
                return $req;
            }            
            
            return $database->query($query);
        }
        catch(Exception $e){        
            die('Erreur :'.$e->getMessage());
        }
    } 

    function findAll() {
        return render(db_query('SELECT * FROM contact ORDER BY id DESC')->fetchAll());        
    }

    function find(int $id) {
        return render(db_query('SELECT * FROM contact WHERE id=:id', ['id'=> $id ])->fetch());
    }

    function update(int $id) {
       // return db_query('UPDATE contact SET col = value WHERE id=:id', ['id'=> $id ])->fetch();
    }

    function delete(int $id) {
        //return db_query('DELETE FROM contact WHERE id=:id', ['id'=> $id ]);
    }







