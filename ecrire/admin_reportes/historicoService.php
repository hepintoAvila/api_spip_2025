<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
 
	
class HistoricoService {

	public function getHistoricos($data) {
		try {
			$nombreVista = $this->getVisita($data);
			$totalRegistrosGlobal = $this->getTotalRegistros($data);
			
			$totalEstudiantes = $this->getTotalEstudiantes();
			
			if ($totalRegistrosGlobal == 0) {
			  $porcentaje = 0; // o cualquier otro valor que tenga sentido en tu aplicación
			} else {
			  $porcentaje = round(($totalEstudiantes / $totalRegistrosGlobal) * 100, 3);
			}
			
			$totaMes = $this->getTotalMes($nombreVista);
			$resProgramas = $this->getDataProgramas($data);
			$totalManana = $this->getTotalRegistrosTurno($nombreVista,'total_manana');
			
			$totalTarde = $this->getTotalRegistrosTurno($nombreVista,'total_tarde');
			$totalNocturna = $this->getTotalRegistrosTurno($nombreVista,'total_noche');
			$totalRegistros = $this->getTotalRegistrosTurno($nombreVista,'total_registros');
			 
			$porcJornada= $this->calcularPorcentaje(intval($totalRegistros),intval($totalRegistrosGlobal));
			
			$porcManana= $this->calcularPorcentaje(intval($totalRegistros),intval($totalManana));
			$porcTarde= $this->calcularPorcentaje(intval($totalRegistros),intval($totalTarde));
			$porcNocturna= $this->calcularPorcentaje(intval($totalRegistros),intval($totalNocturna));
			
			 
			$datosWidget = [
				'Historicos' => [
					[
						'title' =>$this->getUbicaciones($data),
						'description' => $this->getUbicaciones($data),
						'stats' => $totalRegistros,
						'trend' => [
							'textClass' => 'text-success',
							'icon' => 'mdi mdi-arrow-up-bold',
							'value' => $porcentaje . '%'
						],
						'colors' => ['#d4212c'],
						'data' => $this->getData($nombreVista,'total_registros'),
						'dataTotales' => array(round($porcJornada, 3),round($porcManana, 3),round($porcTarde, 3),round($porcNocturna, 3)),
						'dataColors' => array('#f6aa38','#2f9dd8','#a43ac1','#d4212c'),
						'dataMeses' => array($totaMes),
						'dataProgramas' => $resProgramas,
					],
					 
					[
						'title' => 'Mañana',
						'description' => $this->getUbicaciones($data),
						'stats' => intval($totalManana),
						'trend' => [
							'textClass' => intval($totalManana) < 200 ? 'text-danger' : 'text-success',
							'icon' => intval($totalManana) < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => intval($porcManana) . '%'
						],
						'colors' => ['#f6aa38'],
						'data' => $this->getData($nombreVista,'porcentaje_manana')
					],
					
					[
						'title' => 'Tarde',
						'description' => $this->getUbicaciones($data),
						'stats' => $totalTarde,
						'trend' => [
							'textClass' => intval($totalTarde) < 200 ? 'text-danger' : 'text-success',
							'icon' => intval($totalTarde) < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $porcTarde . '%'
						],
						'colors' => ['#2f9dd8'],
						'data' => $this->getData($nombreVista,'porcentaje_tarde')
					],
					[
						'title' => 'Nocturna',
						'description' =>$this->getUbicaciones($data),
						'stats' => $totalNocturna,
						'trend' => [
							'textClass' => intval($totalNocturna) < 200 ? 'text-danger' : 'text-success',
							'icon' => intval($totalNocturna) < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $porcNocturna . '%'
						],
						'colors' => ['#a43ac1'],
						'data' => $this->getData($nombreVista,'porcentaje_noche')
					]
					 
				]
			];
			
				if (!empty($datosWidget['Historicos'])) {
					return $datosWidget;
				} else {
					return array('Historicos'=>array());
				}
		 	 
		} catch (Exception $e) {
			return json_encode(['error' => $e->getMessage()]);
		}
		
	}

	private function getTotalRegistros($data) {
			$row = sql_fetsel("COUNT(*) AS total", "upc_hist_libros_visita JOIN upc_tipo_programas ON upc_hist_libros_visita.programa = upc_tipo_programas.id_programa WHERE upc_tipo_programas.nombre_programa = '".$data['programa']."' AND upc_hist_libros_visita.ubicacion = 3 AND YEAR(upc_hist_libros_visita.fecha) = '".$data['fecha']."'", "");
			return intval($row['total']);
		}

	private function getTotalEstudiantes() {
		$row = sql_fetsel("COUNT(DISTINCT(PEGE_DOCUMENTOIDENTIDAD)) AS TOTALESTUDIANTES", "upc_estudiantes", "");
		return $row['TOTALESTUDIANTES'];
	}
	private function getUbicaciones($data) {
		$row = sql_fetsel("titulo", "upc_tipo_ubicaciones", "id_tipo='".$data['ubicacion']."'");
		return utf8_encode($row['titulo']);
	}	
	private function getTotalMes($nombreVista) {
		//SELECT SUM(total_registros),mes FROM `upc_resumen_turnos` GROUP BY mes;
			$res = sql_select('SUM(total_registros) as totales,mes', "$nombreVista GROUP BY mes", "");
		$data = [];
		while ($r = sql_fetch($res)) {
			$data[$r['mes']] = round($r['totales'],3);
		}
		return $data;
	}
	
		private function getDataProgramas($data) {
		  $id_programa = $this->getIdPrograma($data['programa']);
		  $progamas = array('PROG_NOMBRE' => '', 'mes' => '00', 'turno_tipo' => '0', 'cantidad' => '0');
		  $select = 'E.nombre_programa AS PROG_NOMBRE, MONTH(R.fecha) AS mes,determinar_turno(TIME(R.fecha)) AS turno_tipo, COUNT(*) AS cantidad';
		  $where ="R.programa = '" . sql_quote($id_programa) . "' AND YEAR(R.fecha) = '" . sql_quote($data['fecha']) . "' AND R.programa = E.id_programa GROUP BY E.nombre_programa,MONTH(R.fecha),determinar_turno(TIME(R.fecha))";
		  
		  $res = sql_select($select,"upc_hist_libros_visita AS R,upc_tipo_programas AS E",$where);
		  if (!$res) {
			return [$progamas]; // Retorna un array con $progamas
		  }
		  
		  $data = [];
		  while ($row = sql_fetch($res)) {
			$data[] = $row;
		  }
		  
		  return $data;
		 
		}	
	private function getData($nombreVista,$campo) {
		$res = sql_select("".$campo."",''.$nombreVista.'', "");
		$data = [];
		while ($r = sql_fetch($res)) {
			$data[] = $r[$campo];
		}
		return $data;
	}

	private function getTotalRegistrosTurno($nombreVista,$campo) {
		  $row = sql_fetsel("SUM($campo) AS TOTAL",''.$nombreVista.'', "");
		 return $row['TOTAL'];
	}
	private function calcularPorcentaje($totalRegistros,$totalRegistrosTurno) {
		 
		if ($totalRegistros == 0) {
			return 0;
		}
		if ($totalRegistrosTurno == 0) {
			return 0;
		}
		return round((intval($totalRegistrosTurno) / intval($totalRegistros)) * 100, 3);
	}
	
	private function getVisita($data) {
			$id_programa=$this->getIdPrograma($data['programa']);
			return $this->consultar_vista($id_programa, $data['fecha'], $data['ubicacion']);
		}	
	private function getIdPrograma($programa) {
			$row = sql_fetsel("id_programa", "upc_tipo_programas","nombre_programa=".sql_quote($programa)."");
			return intval($row['id_programa']);
		}
	private function consultar_vista($programa, $fecha, $ubicacion) {
			  $query = "CALL crear_vista_si_no_existe($programa, $fecha,$ubicacion)";
			  spip_mysql_query($query);  
			  $nombre_vista = "vista_" . $programa . "_" . $fecha. "_" . $ubicacion;		
			return $nombre_vista;	 
		}
	
}