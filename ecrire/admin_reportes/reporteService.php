<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class ReporteService {

	public function getChartWidget() {
		try {
			$totalRegistros = $this->getTotalRegistros();
			$totalEstudiantes = $this->getTotalEstudiantes();
			$porcentaje = round(($totalEstudiantes / $totalRegistros) * 100, 3);
			$totaMes = $this->getTotalMes();
			$resProgramas = $this->getDataProgramas();
			$totalManana = $this->getTotalRegistrosTurno('total_mañana');
			$totalTarde = $this->getTotalRegistrosTurno('total_tarde');
			$totalNocturna = $this->getTotalRegistrosTurno('total_nocturna');
			$totaljornada = $totalManana+$totalTarde+$totalNocturna;
			
			$porcJornada= $this->calcularPorcentaje(intval($totalRegistros),intval($totaljornada));
			$porcManana= $this->calcularPorcentaje(intval($totalRegistros),intval($totalManana));
			$porcTarde= $this->calcularPorcentaje(intval($totalRegistros),intval($totalTarde));
			$porcNocturna= $this->calcularPorcentaje(intval($totalRegistros),intval($totalNocturna));

			$datosWidget = [
				'chartwidget' => [
					[
						'title' => 'Aulavirtual',
						'description' => 'Visitas Aulavirtual',
						'stats' => $totalRegistros,
						'trend' => [
							'textClass' => 'text-success',
							'icon' => 'mdi mdi-arrow-up-bold',
							'value' => $porcentaje . '%'
						],
						'colors' => ['#d4212c'],
						'data' => $this->getData('total_registros'),
						'dataTotales' => array(round($porcJornada, 3),round($porcManana, 3),round($porcTarde, 3),round($porcNocturna, 3)),
						'dataColors' => array('#f6aa38','#2f9dd8','#a43ac1','#d4212c'),
						'dataMeses' => array($totaMes),
						'dataProgramas' => $resProgramas,
					],					
					[
						'title' => 'Mañana',
						'description' => 'Visitas Aulavirtual',
						'stats' => $totalManana,
						'trend' => [
							'textClass' => $totalManana < 200 ? 'text-danger' : 'text-success',
							'icon' => $totalManana < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $porcManana . '%'
						],
						'colors' => ['#f6aa38'],
						'data' => $this->getData('porcentaje_mañana')
					],
					[
						'title' => 'Tarde',
						'description' => 'Visitas Aulavirtual',
						'stats' => $totalTarde,
						'trend' => [
							'textClass' => $totalTarde < 200 ? 'text-danger' : 'text-success',
							'icon' => $totalTarde < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $porcTarde . '%'
						],
						'colors' => ['#2f9dd8'],
						'data' => $this->getData('porcentaje_tarde')
					],
					[
						'title' => 'Nocturna',
						'description' => 'Visitas Aulavirtual',
						'stats' => $totalNocturna,
						'trend' => [
							'textClass' => $totalNocturna < 200 ? 'text-danger' : 'text-success',
							'icon' => $totalNocturna < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $porcNocturna . '%'
						],
						'colors' => ['#a43ac1'],
						'data' => $this->getData('porcentaje_nocturna')
					]
				]
			];
				if (!empty($datosWidget['chartwidget'])) {
					return $datosWidget;
				} else {
					return array('chartwidget'=>array());
				}
		} catch (Exception $e) {
			return json_encode(['error' => $e->getMessage()]);
		}
		
	}

	private function getTotalRegistros() {
	  $resultado = sql_fetsel("SUM(R.total_registros) AS TOTALPRESTADOS", "upc_resumen_turnos AS R", "");
	  return $resultado['TOTALPRESTADOS'];
	}

	private function getTotalEstudiantes() {
		$row = sql_fetsel("COUNT(DISTINCT(PEGE_DOCUMENTOIDENTIDAD)) AS TOTALESTUDIANTES", "upc_estudiantes", "");
		return $row['TOTALESTUDIANTES'];
	}
	
	private function getTotalMes() {
		//SELECT SUM(total_registros),mes FROM `upc_resumen_turnos` GROUP BY mes;
			$res = sql_select('SUM(total_registros) as totales,mes', "upc_resumen_turnos GROUP BY mes", "");
		$data = [];
		while ($r = sql_fetch($res)) {
			$data[$r['mes']] = round($r['totales'],3);
		}
		return $data;
	}

 
	
	private function getDataProgramas() {
	$res = sql_select('E.PROG_NOMBRE, 
  MONTH(R.fecha_creacion) AS mes,
  determinar_turno(TIME(R.fecha_creacion)) AS turno_tipo,
  COUNT(*) AS cantidad', "upc_turnos AS R 
INNER JOIN upc_estudiantes AS E 
  ON R.documento = E.PEGE_DOCUMENTOIDENTIDAD", "R.statut = 'Activo' AND E.PROG_NOMBRE IS NOT NULL AND E.PROG_NOMBRE != ''
GROUP BY E.PROG_NOMBRE, MONTH(R.fecha_creacion), determinar_turno(TIME(R.fecha_creacion))");
			$data = [];
			while ($r = sql_fetch($res)) {
				$data[] = $r;
			}
			return $data;
		}	
	private function getData($campo) {
		$res = sql_select("".$campo."", "upc_resumen_turnos", "");
		$data = [];
		while ($r = sql_fetch($res)) {
			$data[] = $r[$campo];
		}
		return $data;
	}

	private function getTotalRegistrosTurno($campo) {
		 $row = sql_fetsel("SUM($campo) AS TOTAL", "upc_resumen_turnos AS R", "");
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
	//APLICA PARA LIBRO DE Visitas
	
	
}