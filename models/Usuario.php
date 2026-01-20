<?php
/**
 * Modelo Usuario
 * Gestión de usuarios del sistema
 */

require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $telefono;
    public $estado;
    public $fecha_creacion;
    public $ultimo_acceso;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Autenticar usuario
     */
    public function autenticar($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email AND estado = 'activo' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            if (password_verify($password, $row['password'])) {
                // Actualizar último acceso
                $this->actualizarUltimoAcceso($row['id']);
                
                return [
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'email' => $row['email'],
                    'rol' => $row['rol'],
                    'telefono' => $row['telefono']
                ];
            }
        }
        return false;
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(nombre LIKE ? OR email LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['rol'])) {
            $where[] = "rol = ?";
            $params[] = $filtros['rol'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = "estado = ?";
            $params[] = $filtros['estado'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT id, nombre, email, rol, telefono, estado, fecha_creacion, ultimo_acceso 
                  FROM " . $this->table . " 
                  $whereClause 
                  ORDER BY fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT id, nombre, email, rol, telefono, estado, fecha_creacion, ultimo_acceso 
                  FROM " . $this->table . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, password, rol, telefono, estado) 
                  VALUES (:nombre, :email, :password, :rol, :telefono, :estado)";

        $stmt = $this->conn->prepare($query);

        // Hash de la contraseña
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':rol', $datos['rol']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':estado', $datos['estado']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($datos['nombre'])) {
            $fields[] = "nombre = :nombre";
            $params[':nombre'] = $datos['nombre'];
        }

        if (isset($datos['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $datos['email'];
        }

        if (isset($datos['password']) && !empty($datos['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        if (isset($datos['rol'])) {
            $fields[] = "rol = :rol";
            $params[':rol'] = $datos['rol'];
        }

        if (isset($datos['telefono'])) {
            $fields[] = "telefono = :telefono";
            $params[':telefono'] = $datos['telefono'];
        }

        if (isset($datos['estado'])) {
            $fields[] = "estado = :estado";
            $params[':estado'] = $datos['estado'];
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Verificar si el email ya existe
     */
    public function emailExiste($email, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Actualizar último acceso
     */
    private function actualizarUltimoAcceso($id) {
        $query = "UPDATE " . $this->table . " SET ultimo_acceso = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($id, $password_actual, $password_nueva) {
        // Verificar contraseña actual
        $query = "SELECT password FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            if (password_verify($password_actual, $row['password'])) {
                // Actualizar con la nueva contraseña
                $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':id', $id);
                return $stmt->execute();
            }
        }
        return false;
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN rol = 'administrador' THEN 1 ELSE 0 END) as administradores,
                    SUM(CASE WHEN rol = 'vendedor' THEN 1 ELSE 0 END) as vendedores,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}
