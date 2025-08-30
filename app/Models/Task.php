<?php
namespace App\Models;

class Task {
    public ?int $id;
    public string $title;
    public ?string $description;
    public string $status;

    public function __construct($data){
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->status = $data['status'] ?? 'pendiente';
    }
}
