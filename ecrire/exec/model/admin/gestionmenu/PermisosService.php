
<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('exec/model/admin/claseapi');

class PermisosService {
  private $aut;

 
  public function getPermisos($aut) {
    if (!$this->validateAut($aut)) {
      throw new Exception('Datos de autenticaci�n incompletos');
    }
    $this->aut = $aut;
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

  private function getRoles($idTipo) {
    $app = new Apis('apis_menu AS M, apis_submenus AS S, apis_autorizaciones AS A, apis_roles as R');
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
    return $app->consultapermmisos($query, $select, array());
  }

  private function formatPermisos($roles) {
    $permisos = array('Permisos' => array());
    if (!empty($roles)) {
      foreach ($roles as $row) {
        $pos = strrpos($row['submenu'], '/');
        $submenuPermiso = ($pos !== false) ? substr($row['submenu'], $pos + 1) : $row['submenu'];
        $permisos['Permisos'][] = array(
          'query' => $row['c'],
          'add' => $row['a'],
          'update' => $row['u'],
          'delete' => $row['d'],
          'menu' => $row['menu'],
          'submenu' => $submenuPermiso
        );
      }
    } else {
      $permisos['Permisos'][] = array(
        'query' => null,
        'add' => null,
        'update' => null,
        'delete' => null,
        'menu' => null,
        'submenu' => null
      );
    }
    return $permisos;
  }
}