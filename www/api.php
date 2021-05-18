<?php
define('DB_HOST', 'db');
define('DB_USER', 'root');
define('DB_PASS', 'todo-app-pass');
define('DB_NAME', 'todo');

try
{
    $db = new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS );

    // Parse data
    $data = json_decode(file_get_contents('php://input'), true);
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $result = $db->query("SELECT id, title, completed FROM todo");
            $result->execute();
            $items = $result->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($items, JSON_NUMERIC_CHECK);
            break;
        case 'PUT':
            if (isset($data['completed']))
            {
                $result = $db->prepare("UPDATE todo SET title=?, completed=? WHERE id=?");
                $result->bindValue(1, $data['title'], PDO::PARAM_STR);
                $result->bindValue(2, $data['completed'], PDO::PARAM_INT);
                $result->bindValue(3, $data['id'], PDO::PARAM_INT);
            }
            else
            {
                $result = $db->prepare("UPDATE todo SET title=? WHERE id=?");
                $result->bindValue(1, $data['title'], PDO::PARAM_STR);
                $result->bindValue(2, $data['id'], PDO::PARAM_INT);
            }
            $result->execute();
            echo json_encode(array('status' => 'UPDATE OK'));
            break;
        case 'POST':
            $result = $db->prepare("INSERT INTO todo (title) VALUES (?)");
            $result->bindValue(1, $data['title'], PDO::PARAM_STR);
            $result->execute();
            echo json_encode(array('id' => $db->lastInsertId()));
            break;
        case 'DELETE':
            $result = $db->prepare("DELETE FROM todo WHERE id=?");
            $result->bindValue(1, $data['id'], PDO::PARAM_INT);
            $result->execute();
            echo json_encode(array('status' => 'DELETE OK'));
            break;
        default:
            http_response_code(400);
            break;	
    }
}
catch ( Exception $e )
{
    echo 'Server Error: ' . $e->getMessage();
}