<?php

require_once __DIR__ . '/../config/Database.php';

class Boleto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function obtenerDetalleFuncion(int $idFuncion): array|false
    {
        $sql = "SELECT f.id_funcion, f.fecha_hora, f.precio_boleto, f.formato,
                       TIME_FORMAT(f.fecha_hora, '%h:%i %p') AS horario_formato,
                       s.id_sala, s.nombre_sala, s.capacidad,
                       p.id_pelicula, p.titulo AS pelicula, p.clasificacion, p.duracion
                FROM funciones f
                INNER JOIN salas s ON f.id_sala = s.id_sala
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                WHERE f.id_funcion = :id_funcion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_funcion', $idFuncion, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $data['horario_formato'] = strtolower($data['horario_formato']);
        }

        return $data;
    }

    public function obtenerButacasOcupadas(int $idFuncion): array
    {
        $sql = "SELECT fila_columna FROM boletos WHERE id_funcion = :id_funcion";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_funcion', $idFuncion, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
