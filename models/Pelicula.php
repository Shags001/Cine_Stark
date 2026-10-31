<?php

require_once __DIR__ . '/../config/Database.php';

class Pelicula
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listarTodas(): array
    {
        $sql = "SELECT id_pelicula, titulo, clasificacion, duracion, imagen
                FROM peliculas
                ORDER BY titulo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_pelicula, titulo, clasificacion, duracion, imagen
                FROM peliculas
                WHERE id_pelicula = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(string $titulo, string $clasificacion, int $duracion, string $imagen): int
    {
        $sql = "INSERT INTO peliculas (titulo, clasificacion, duracion, imagen)
                VALUES (:titulo, :clasificacion, :duracion, :imagen)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':clasificacion', $clasificacion, PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagen, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, string $titulo, string $clasificacion, int $duracion, string $imagen): bool
    {
        $sql = "UPDATE peliculas
                SET titulo = :titulo, clasificacion = :clasificacion,
                    duracion = :duracion, imagen = :imagen
                WHERE id_pelicula = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':clasificacion', $clasificacion, PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagen, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Elimina una pelicula por su identificador
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM peliculas WHERE id_pelicula = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cuenta cuantas funciones tiene programadas la pelicula
    public function tieneFunciones(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM funciones WHERE id_pelicula = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    // Verifica si alguna funcion de la pelicula tiene boletos vendidos
    public function tieneBoletosVendidos(int $id): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM boletos b
                INNER JOIN funciones f ON b.id_funcion = f.id_funcion
                WHERE f.id_pelicula = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
