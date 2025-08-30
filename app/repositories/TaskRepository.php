<?php
namespace App\Repositories;
use App\Database\Connection;

class TaskRepository {
    private $pdo;
    public function __construct(){ $this->pdo = Connection::get(); }

    public function all(){
        $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY id');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function find($id){
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    public function create(array $data){
        $stmt = $this->pdo->prepare('INSERT INTO tasks (title, description, status) VALUES (:title,:description,:status) RETURNING *');
        $stmt->execute(['title'=>$data['title'],'description'=>$data['description'] ?? null,'status'=>$data['status'] ?? 'pendiente']);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function update($id, array $data){
        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare('UPDATE tasks SET title=:title, description=:description, status=:status, updated_at=now() WHERE id=:id');
        $stmt->execute(['title'=>$data['title'],'description'=>$data['description'] ?? null,'status'=>$data['status'] ?? 'pendiente','id'=>$id]);
        $this->pdo->commit();
        return $this->find($id);
    }
    public function delete($id){
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id=:id');
        return $stmt->execute(['id'=>$id]);
    }
}
