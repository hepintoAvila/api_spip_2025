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



if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
class MenuService {
  private $idRol;

  public function getMenu($aut) {
    try {
      $this->idRol = $this->getIdRol($aut['tipo']);
      $menusMain = $this->getMenusMain();
      return array('Menus' => $menusMain);
    } catch (Exception $e) {
      return array('error' => $e->getMessage());
    }
  }

  private function getIdRol($tipo) {
    $sql = sql_select('idRol', 'apis_roles', 'tipo=' . sql_quote($tipo));
    $row = sql_fetch($sql);
    if (!$row) {
      throw new Exception('No se encontró el rol');
    }
    return $row['idRol']; 
  }
  public function getMenus() {
    $sql = sql_select("DISTINCT M.idMenu,M.key,M.label,M.isTitle,M.icon",
      'apis_menu AS M',
      "M.status='Active' ORDER BY M.idMenu");
    $menusMain = array();
    while ($r = sql_fetch($sql)) {
      $children = $this->getSubMenu($r['idMenu']);
      $menusMain[] = array(
        'idMenu' => $r['idMenu'],
        'key' => $r['key'],
        'label' => $r['label'],
        'isTitle' => false,
        'icon' => $r['icon'],
        'badge' => array('variant' => 'error', 'text' => count($children)),
        'children' => $children
      );
    }
    return $menusMain;
  }
  private function getMenusMain() {
    $sql = sql_select("DISTINCT M.idMenu,M.key,M.label,M.isTitle,M.icon",
      'apis_autorizaciones as A,apis_roles as R,apis_menu AS M',
      "A.idRol='" . $this->idRol . "' 
      AND R.idRol=A.idRol 
      AND NULLIF(A.c,'')='S' AND M.status='Active' ORDER BY M.idMenu");
    $menusMain = array();
    while ($r = sql_fetch($sql)) {
      $children = $this->getChildren($r['idMenu']);
      $menusMain[] = array(
        'key' => $r['key'],
        'label' => $r['label'],
        'isTitle' => false,
        'icon' => $r['icon'],
        'badge' => array('variant' => 'error', 'text' => count($children)),
        'children' => $children
      );
    }
    return $menusMain;
  }
  private function getSubMenu($idMenu) {
    $sql = sql_select('S.key,S.label,S.icon,S.url,M.key AS parentKey,S.idSubmenu',
      'apis_menu AS M,apis_submenus AS S',
      "M.idMenu= S.idMenu 
      AND M.idMenu='" . $idMenu . "'  
      AND S.status='Active' ORDER BY S.idSubmenu ASC");
    $children = array();
    while ($ch = sql_fetch($sql)) {
      $children[] = array(
        'idSubmenu' => $ch['idSubmenu'],
        'key' => $ch['key'],
        'label' => $ch['label'],
        'url' => $ch['url'],
        'parentKey' => $ch['parentKey'],
        'icon' => $ch['icon']
      );
    }
    return $children;
  }

  private function getChildren($idMenu) {
    $sql = sql_select('DISTINCT S.key,S.label,S.icon,S.url,M.key AS parentKey',
      'apis_menu AS M,apis_submenus AS S, apis_autorizaciones AS A,apis_roles as R',
      "R.idRol='" . $this->idRol . "' 
      AND A.idRol= R.idRol 
      AND M.idMenu= A.idMenu 
      AND A.idMenu='" . $idMenu . "'  
      AND A.c='S' 
      AND A.idSubmenu=S.idSubmenu 
      AND S.status='Active' ORDER BY S.idSubmenu ASC");
    $children = array();
    while ($ch = sql_fetch($sql)) {
      $children[] = array(
        'key' => $ch['key'],
        'label' => $ch['label'],
        'url' => $ch['url'],
        'parentKey' => $ch['parentKey'],
        'icon' => $ch['icon']
      );
    }
    return $children;
  }
}