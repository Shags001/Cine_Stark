<?php

require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function autenticar(string $usuario, string $password): array|false
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.usuario, u.password, u.estado,
                       u.id_rol, r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.usuario = :usuario
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $activo = ($row['estado'] === 'Activo' || (int)$row['estado'] === 1);
        if (!$activo) {
            return false;
        }

        $hashValid = password_verify($password, $row['password']);
        $plainValid = ($password === $row['password']);

        if (!$hashValid && !$plainValid) {
            return false;
        }

        return [
            'id_usuario' => $row['id_usuario'],
            'nombre'     => $row['nombre'],
            'id_rol'     => $row['id_rol'],
            'nombre_rol' => $row['nombre_rol'],
        ];
    }

    public function listarTodos(): array
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.usuario, u.estado,
                       u.id_rol, r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                ORDER BY u.nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.usuario, u.estado,
                       u.id_rol, r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, string $usuario, string $password, int $idRol): int
    {
        $sql = "INSERT INTO usuarios (nombre, usuario, password, id_rol, estado)
                VALUES (:nombre, :usuario, :password, :id_rol, 'Activo')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id_rol', $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, string $nombre, string $usuario, int $idRol): bool
    {
        $sql = "UPDATE usuarios
                SET nombre = :nombre, usuario = :usuario, id_rol = :id_rol
                WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->bindParam(':id_rol', $idRol, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cambiarPassword(int $id, string $password): bool
    {
        $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function existeUsuario(string $usuario, int $excluirId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario AND id_usuario != :excluir";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->bindParam(':excluir', $excluirId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function listarRoles(): array
    {
        $sql = "SELECT id_rol, nombre_rol, descripcion FROM roles ORDER BY id_rol ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
