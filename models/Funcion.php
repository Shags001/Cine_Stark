<?php

require_once __DIR__ . '/../config/Database.php';

class Funcion
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Obtiene las funciones de la fecha agrupadas por pelicula y sala
    public function obtenerFuncionesDelDia(?string $fecha = null): array
    {
        $fechaConsulta = $fecha ?: date('Y-m-d');

        $sql = "SELECT s.id_sala, s.nombre_sala, s.capacidad,
                       p.id_pelicula, p.titulo, p.clasificacion, p.duracion, p.imagen,
                       f.id_funcion, f.fecha_hora, f.precio_boleto, f.formato,
                       TIME_FORMAT(f.fecha_hora, '%h:%i %p') AS horario_formato
                FROM salas s
                INNER JOIN funciones f ON s.id_sala = f.id_sala
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                WHERE DATE(f.fecha_hora) = :fecha
                ORDER BY s.id_sala ASC, f.fecha_hora ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fecha' => $fechaConsulta]);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupadas = [];
        foreach ($registros as $row) {
            $clave = $row['id_pelicula'] . '_' . $row['id_sala'];
            if (!isset($agrupadas[$clave])) {
                $agrupadas[$clave] = [
                    'id_sala'       => $row['id_sala'],
                    'nombre_sala'   => $row['nombre_sala'],
                    'capacidad'     => $row['capacidad'],
                    'id_pelicula'   => $row['id_pelicula'],
                    'pelicula'      => $row['titulo'],
                    'clasificacion' => $row['clasificacion'],
                    'duracion'      => $row['duracion'],
                    'formato'       => $row['formato'],
                    'imagen'        => $row['imagen'] ?? '',
                    'horarios'      => []
                ];
            }

            $agrupadas[$clave]['horarios'][] = [
                'id_funcion'      => $row['id_funcion'],
                'horario_formato' => strtolower($row['horario_formato']),
                'precio_boleto'   => $row['precio_boleto'],
                'formato'         => $row['formato']
            ];
        }

        return array_values($agrupadas);
    }

    public function listarTodas(): array
    {
        $sql = "SELECT f.id_funcion, f.id_pelicula, f.id_sala, f.fecha_hora, f.precio_boleto, f.formato,
                       p.titulo AS pelicula_titulo, p.duracion, p.clasificacion, p.imagen,
                       s.nombre_sala, s.capacidad,
                       DATE_FORMAT(f.fecha_hora, '%d/%m/%Y') AS fecha_formato,
                       TIME_FORMAT(f.fecha_hora, '%h:%i %p') AS hora_formato
                FROM funciones f
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                INNER JOIN salas s ON f.id_sala = s.id_sala
                ORDER BY f.fecha_hora DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT f.id_funcion, f.id_pelicula, f.id_sala, f.fecha_hora, f.precio_boleto, f.formato,
                       p.titulo AS pelicula_titulo, s.nombre_sala
                FROM funciones f
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                INNER JOIN salas s ON f.id_sala = s.id_sala
                WHERE f.id_funcion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(int $idPelicula, int $idSala, string $fechaHora, float $precio, string $formato): int
    {
        $sql = "INSERT INTO funciones (id_pelicula, id_sala, fecha_hora, precio_boleto, formato)
                VALUES (:id_pelicula, :id_sala, :fecha_hora, :precio, :formato)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pelicula', $idPelicula, PDO::PARAM_INT);
        $stmt->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $fechaHora, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':formato', $formato, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, int $idPelicula, int $idSala, string $fechaHora, float $precio, string $formato): bool
    {
        $sql = "UPDATE funciones
                SET id_pelicula = :id_pelicula, id_sala = :id_sala,
                    fecha_hora = :fecha_hora, precio_boleto = :precio, formato = :formato
                WHERE id_funcion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pelicula', $idPelicula, PDO::PARAM_INT);
        $stmt->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $fechaHora, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':formato', $formato, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM funciones WHERE id_funcion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function tieneBoletosVendidos(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM boletos WHERE id_funcion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Verifica si existe una función que se traslape con el horario solicitado en una sala,
     * tomando en cuenta la duración de la nueva película y la duración de las películas ya agendadas.
     * Retorna los datos del conflicto o null si el horario está totalmente libre.
     */
    public function verificarSolapamientoHorario(int $idSala, string $inicioNuevo, int $duracionMinutos, int $excluirId = 0): ?array
    {
        // Dos intervalos [A_inicio, A_fin] y [B_inicio, B_fin] se solapan si:
        // A_inicio < B_fin AND B_inicio < A_fin
        $sql = "SELECT f.id_funcion, f.fecha_hora, p.titulo AS pelicula_titulo, p.duracion,
                       DATE_ADD(f.fecha_hora, INTERVAL p.duracion MINUTE) AS fecha_hora_fin,
                       s.nombre_sala
                FROM funciones f
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                INNER JOIN salas s ON f.id_sala = s.id_sala
                WHERE f.id_sala = :id_sala
                  AND f.id_funcion != :excluir_id
                  AND :inicio_nuevo < DATE_ADD(f.fecha_hora, INTERVAL p.duracion MINUTE)
                  AND f.fecha_hora < DATE_ADD(:inicio_nuevo_fin, INTERVAL :duracion_nueva MINUTE)
                ORDER BY f.fecha_hora ASC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        $stmt->bindParam(':excluir_id', $excluirId, PDO::PARAM_INT);
        $stmt->bindParam(':inicio_nuevo', $inicioNuevo, PDO::PARAM_STR);
        $stmt->bindParam(':inicio_nuevo_fin', $inicioNuevo, PDO::PARAM_STR);
        $stmt->bindParam(':duracion_nueva', $duracionMinutos, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    // Verifica si la sala ya tiene una funcion en esa fecha y horario exacto (mantenido por compatibilidad)
    public function existeFuncionEnHorario(int $idSala, string $fechaHora, int $excluirId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM funciones 
                WHERE id_sala = :id_sala 
                  AND fecha_hora = :fecha_hora 
                  AND id_funcion != :excluir_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $fechaHora, PDO::PARAM_STR);
        $stmt->bindParam(':excluir_id', $excluirId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
