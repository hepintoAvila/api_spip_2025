<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 

		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('inc/actions');
		include_spip('ecrire/classes/classgeneral');
	
class PermisoService {
    private $app;
    private $apis;
    private $autoriza;
    public $data;
    private $aut;
    private $table;

    public function __construct($data) {
        $this->data = $data;
        $this->apis = new General('apis_roles');
        $this->autoriza = new General('apis_autorizaciones');
		$this->table = 'apis_menu AS M, apis_submenus AS S, apis_autorizaciones AS A, apis_roles as R';
		$this->aut = $data;
    }

    public function addPermisos($data){
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $chartic debe ser un array');
        }
		$chartic['idMenu']=$this->getId('idMenu','apis_menu',$data['menu']);
		$chartic['idSubmenu']=$this->getId('idSubmenu','apis_submenus',$data['submenu']);
		$chartic['idRol']=$this->getIdRol($data['rol']);
		$chartic['c']=$data['query'];
		$chartic['u']=$data['update'];
		$chartic['d']=$data['delete'];
		$chartic['a']=$data['add'];
        $this->autoriza->guardarDatos($chartic);
    }
	public function updatePermisos($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_autorizacion'])) {
				throw new Exception('El parámetro id_autorizacion es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_autorizacion') {
					$chartic[$key] = $value;
				}
			}

			try {
				$this->apis->actualizarDatos($chartic, 'id_autorizacion', $data['id_autorizacion']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
    public function deletePermiso($arg1){
        sql_delete("apis_roles","id_visita=" . intval($arg1));
    }

    public function getPermisos() {
        if (!$this->validateAut($this->aut)) {
            throw new Exception('Datos de autenticación incompletos');
        }
        
        try {
            $idTipo = $this->getIdTipo();
            $roles = $this->getRoles($idTipo);
            $permisos = $this->formatPermisos($roles);
            return $permisos;
        } catch (Exception $e) {
            return array('error' => $e->getMessage());
        }
    }

    private function validateAut($aut) {
        return isset($aut) && isset($aut['tipo']);
    }

    private function getIdTipo() {
        $sql = sql_select('idRol', 'apis_roles', 'tipo = ' . sql_quote($this->aut['tipo']));
        if (!$sql) {
            throw new Exception('Error al consultar roles');
        }
        $row = sql_fetch($sql);
        if (!$row) {
            throw new Exception('No se encontraron roles para el usuario');
        }
        return $row['idRol'];
    }
	 
	 private function consultapermmisos($query, $select, $campos = null) {
			$sql = sql_select($select,$this->table, $query);
			$datos = array();
			
			if ($sql) {
				// Definir los campos esperados basados en tu SELECT
				$expectedFields = [
					'id_autorizacion',
					'menu',
					'submenu',
					'rol',
					'c',
					'a',
					'u',
					'd'
				];
				$row = array();
				while ($rawRow = sql_fetch($sql)) {
					// Mapear los campos según su posición esperada
					$i = 0;
					foreach ($expectedFields as $field) {
						$row[$field] = array_values($rawRow)[$i] ?? null;
						$i++;
					}
					
					$datos[] = $row;
				}
			}
			
			return $datos;
			}
    private function getRoles($idTipo) {
        // Asegúrate de que la clase Apis esté definida y sea accesible
        
        $select = 'A.id as id_autorizacion,
                  M.key AS menu,
                  S.url AS submenu,
                  R.tipo AS rol,
                  A.c as c,
                  A.a as a,
                  A.u as u,
                  A.d as d';
        $query = "R.idRol = '" . $idTipo . "'
                  AND R.idRol = A.idRol
                  AND M.idMenu = A.idMenu 
                  AND S.idSubmenu = A.idSubmenu  
                  AND A.idSubmenu = S.idSubmenu 
                  AND S.status = 'Active' 
                  ORDER BY S.idSubmenu ASC";
        return $this->consultapermmisos($query, $select, array());
    }

    private function formatPermisos($roles) {
        $permisos = array_map(function($row) {
            $pos = strrpos($row['submenu'], '/');
            $submenuPermiso = ($pos !== false) ? substr($row['submenu'], $pos + 1) : $row['submenu'];
            return array(
                'query' => $row['c'],
                'add' => $row['a'],
                'update' => $row['u'],
                'delete' => $row['d'],
                'id_autorizacion' => $row['id_autorizacion'],
                'menu' => $row['menu'],
                'submenu' => $submenuPermiso
            );
        }, $roles);
        return array('Permisos' => $permisos);
    }
    
	private function getIdRol($tipo) {
        $sql = sql_select('idRol', 'apis_roles', 'tipo = ' . sql_quote($tipo));
        if (!$sql) {
            throw new Exception('Error al consultar roles');
        }
        $row = sql_fetch($sql);
        if (!$row) {
            throw new Exception('No se encontraron roles para el usuario');
        }
        return $row['idRol'];
    }
	private function getId($arg1,$arg2,$arg3) {
		
		$sql = sql_select(''.$arg1.' AS id',''.$arg2.'','label='.sql_quote($arg3).'');
		$row = sql_fetch($sql);
		if (!$row) {
		  throw new Exception('No se encontró el rol');
		}
		return $row['id']; 
	  }
}