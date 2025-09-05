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
			$porcentaje = $this->calcularPorcentaje($totalRegistros, $totalEstudiantes);
			$totaMes = $this->getTotalMes();
			$resProgramas = $this->getDataProgramas();
		 
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
						'dataTotales' => array(intval($this->calcularPorcentajeTurno($totalRegistros,'total_mañana')),intval($this->calcularPorcentajeTurno($totalRegistros,'total_tarde')),intval($this->calcularPorcentajeTurno($totalRegistros,'total_nocturna')),intval($totalRegistros)),
						'dataColors' => array('#f6aa38','#2f9dd8','#a43ac1','#d4212c'),
						'dataMeses' => array($totaMes),
						'dataProgramas' => $resProgramas,
					],
					[
						'title' => 'Mañana',
						'description' => 'Visitas Aulavirtual',
						'stats' => $this->getTotalRegistrosTurno('total_mañana'),
						'trend' => [
							'textClass' => $this->getTotalRegistrosTurno('total_mañana') < 200 ? 'text-danger' : 'text-success',
							'icon' => $this->getTotalRegistrosTurno('total_mañana') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $this->calcularPorcentajeTurno($totalRegistros, 'total_mañana') . '%'
						],
						'colors' => ['#f6aa38'],
						'data' => $this->getData('porcentaje_mañana')
					],
					[
						'title' => 'Tarde',
						'description' => 'Visitas Aulavirtual',
						'stats' => $this->getTotalRegistrosTurno('total_tarde'),
						'trend' => [
							'textClass' => $this->getTotalRegistrosTurno('total_tarde') < 200 ? 'text-danger' : 'text-success',
							'icon' => $this->getTotalRegistrosTurno('total_tarde') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $this->calcularPorcentajeTurno($totalRegistros, 'total_tarde') . '%'
						],
						'colors' => ['#2f9dd8'],
						'data' => $this->getData('porcentaje_tarde')
					],
					[
						'title' => 'Nocturna',
						'description' => 'Visitas Aulavirtual',
						'stats' => $this->getTotalRegistrosTurno('total_nocturna'),
						'trend' => [
							'textClass' => $this->getTotalRegistrosTurno('total_nocturna') < 200 ? 'text-danger' : 'text-success',
							'icon' => $this->getTotalRegistrosTurno('total_nocturna') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
							'value' => $this->calcularPorcentajeTurno($totalRegistros, 'total_nocturna') . '%'
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
		$row = sql_fetsel("SUM(R.total_registros) AS TOTALPRESTADOS", "upc_resumen_turnos AS R", "");
		return $row['TOTALPRESTADOS'];
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
			$data[$r['mes']] = $r['totales'];
		}
		return $data;
	}

	private function calcularPorcentaje($totalRegistros, $totalEstudiantes) {
		return round(($totalRegistros / $totalEstudiantes) * 100, 3);
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
		$res = sql_select($campo, "upc_resumen_turnos", "");
		$data = [];
		while ($r = sql_fetch($res)) {
			$data[] = $r[$campo];
		}
		return $data;
	}

	private function getTotalRegistrosTurno($campo) {
		$row = sql_fetsel("SUM(R.$campo) AS TOTAL", "upc_resumen_turnos AS R", "");
		return $row['TOTAL'];
	}
	private function calcularPorcentajeTurno($totalRegistros, $campo) {
		$totalRegistrosTurno = $this->getTotalRegistrosTurno($campo);
		if ($totalRegistros == 0) {
			return 0;
		}
		return round(($totalRegistrosTurno / $totalRegistros) * 100, 3);
	}
	//APLICA PARA LIBRO DE Visitas
	
	
}