<?php
namespace App\Controllers;

use App\Repositories\TaskRepository;
use App\Core\Response;
use App\Services\Validator;

class TaskController {
    private $repo;
    public function __construct(){ $this->repo = new TaskRepository(); }

    public function index(){
        $tasks = $this->repo->all();
        Response::json($tasks);
    }

    public function show($id){
        $task = $this->repo->find((int)$id);
        if (!$task) { Response::json(['error'=>'Tarea no encontrada'],404); return; }
        Response::json($task);
    }

    public function store(){
        $body = json_decode(file_get_contents('php://input'), true);
        $errors = Validator::validateTask($body);
        if ($errors) { Response::json(['errors'=>$errors],422); return; }
        $task = $this->repo->create($body);
        Response::json($task,201);
    }

    public function update($id){
        $body = json_decode(file_get_contents('php://input'), true);
        $errors = Validator::validateTask($body, false);
        if ($errors) { Response::json(['errors'=>$errors],422); return; }
        $existing = $this->repo->find((int)$id);
        if (!$existing) { Response::json(['error'=>'Tarea no encontrada'],404); return; }
        $task = $this->repo->update((int)$id, $body);
        Response::json($task);
    }

    public function destroy($id){
        $existing = $this->repo->find((int)$id);
        if (!$existing) { Response::json(['error'=>'Tarea no encontrada'],404); return; }
        $this->repo->delete((int)$id);
        Response::json(['message'=>'Tarea eliminada']);
    }
}
