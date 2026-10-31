<?php

require_once __DIR__ . '/../config/Database.php';

class Sala
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listarTodas(): array
    {
        $sql = "SELECT id_sala, nombre_sala, capacidad
                FROM salas
                ORDER BY id_sala ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_sala, nombre_sala, capacidad
                FROM salas
                WHERE id_sala = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, int $capacidad): int
    {
        $sql = "INSERT INTO salas (nombre_sala, capacidad)
                VALUES (:nombre, :capacidad)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, string $nombre, int $capacidad): bool
    {
        $sql = "UPDATE salas
                SET nombre_sala = :nombre, capacidad = :capacidad
                WHERE id_sala = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Elimina una sala por su identificador
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM salas WHERE id_sala = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cuenta cuantas funciones tiene asignadas la sala
    public function tieneFunciones(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM funciones WHERE id_sala = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    // Verifica si alguna funcion de la sala tiene boletos vendidos
    public function tieneBoletosVendidos(int $id): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM boletos b
                INNER JOIN funciones f ON b.id_funcion = f.id_funcion
                WHERE f.id_sala = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
