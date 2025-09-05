<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class VisitaService {
	
	public function getChartWidgetVisitas() {
		
    try {
        $totalRegistros = $this->getTotalVisitas();
        $totalEstudiantes = $this->getTotalEstudiantesVisitas();
        $porcentaje = $this->calcularPorcentajeVisitas($totalRegistros, $totalEstudiantes);
        $totaMes = $this->getTotalMesVisitas();
        $resProgramas = $this->getDataProgramasVisitas();

        $datosWidget = [
            'libroVisitas' => [
                [
                    'title' => 'Visitas',
                    'description' => 'Visitas a la biblioteca',
                    'stats' => $totalRegistros,
                    'trend' => [
                        'textClass' => 'text-success',
                        'icon' => 'mdi mdi-arrow-up-bold',
                        'value' => $porcentaje . '%'
                    ],
                    'colors' => ['#d4212c'],
                    'data' => $this->getDataVisitas('total_registros'),
                    'dataTotales' => array(
                        intval($this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_mañana')),
                        intval($this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_tarde')),
                        intval($this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_nocturna')),
                        intval($totalRegistros)
                    ),
                    'dataColors' => array('#f6aa38', '#2f9dd8', '#a43ac1', '#d4212c'),
                    'dataMeses' => array($totaMes),
                    'dataProgramas' => $resProgramas,
                ],
                [
                    'title' => 'Mañana',
                    'description' => 'Visitas a la biblioteca en la mañana',
                    'stats' => $this->getTotalRegistrosTurnoVisitas('total_mañana'),
                    'trend' => [
                        'textClass' => $this->getTotalRegistrosTurnoVisitas('total_mañana') < 200 ? 'text-danger' : 'text-success',
                        'icon' => $this->getTotalRegistrosTurnoVisitas('total_mañana') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
                        'value' => $this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_mañana') . '%'
                    ],
                    'colors' => ['#f6aa38'],
                    'data' => $this->getDataVisitas('total_mañana')
                ],
                [
                    'title' => 'Tarde',
                    'description' => 'Visitas a la biblioteca en la tarde',
                    'stats' => $this->getTotalRegistrosTurnoVisitas('total_tarde'),
                    'trend' => [
                        'textClass' => $this->getTotalRegistrosTurnoVisitas('total_tarde') < 200 ? 'text-danger' : 'text-success',
                        'icon' => $this->getTotalRegistrosTurnoVisitas('total_tarde') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
                        'value' => $this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_tarde') . '%'
                    ],
                    'colors' => ['#2f9dd8'],
                    'data' => $this->getDataVisitas('total_tarde')
                ],
                [
                    'title' => 'Nocturna',
                    'description' => 'Visitas a la biblioteca en la noche',
                    'stats' => $this->getTotalRegistrosTurnoVisitas('total_nocturna'),
                    'trend' => [
                        'textClass' => $this->getTotalRegistrosTurnoVisitas('total_nocturna') < 200 ? 'text-danger' : 'text-success',
                        'icon' => $this->getTotalRegistrosTurnoVisitas('total_nocturna') < 200 ? 'mdi mdi-arrow-down-bold' : 'mdi mdi-arrow-up-bold',
                        'value' => $this->calcularPorcentajeTurnoVisitas($totalRegistros, 'total_nocturna') . '%'
                    ],
                    'colors' => ['#a43ac1'],
                    'data' => $this->getDataVisitas('total_nocturna')
                ]
            ]
        ];

        if (!empty($datosWidget['libroVisitas'])) {
            return $datosWidget;
        } else {
            return array('libroVisitas'=>array());
        }
    } catch (Exception $e) {
        return json_encode(['error' => $e->getMessage()]);
    }
}
	
		private function getTotalVisitas() {
			$row = sql_fetsel('"Totales" AS descripcion,
			  SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) AS total_mañana,
			  SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) AS total_tarde,
			  SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) AS total_nocturna,
			  COUNT(*) AS total_registros,
			  ROUND(SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_mañana,
			  ROUND(SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_tarde,
			  ROUND(SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_nocturna','upc_libros_visita','');
			return intval($row['total_registros']);
		}
		private function getTotalEstudiantesVisitas() {
			$row = sql_fetsel("COUNT(DISTINCT(identificacion)) AS TOTALESTUDIANTES", "upc_libros_visita", "");
			return intval($row['TOTALESTUDIANTES']);
		}

		private function getTotalMesVisitas() {
			$res = sql_select('MONTH(fecha_creacion) AS mes,
			  DAY(fecha_creacion) AS dia,
			  SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) AS total_mañana,
			  SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) AS total_tarde,
			  SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) AS total_nocturna,
			  COUNT(*) AS total_registros,
			  ROUND(SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_mañana,
			  ROUND(SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_tarde,
			  ROUND(SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_nocturna', "upc_libros_visita GROUP BY MONTH(fecha_creacion), DAY(fecha_creacion)", "");
			$data = [];
			while ($r = sql_fetch($res)) {
				$data[$r['mes']] = intval($r['total_registros']);
			}
			return $data;
		}

		private function calcularPorcentajeVisitas($totalRegistros, $totalEstudiantes) {
			return round(($totalRegistros / $totalEstudiantes) * 100, 3);
		}

		private function getDataProgramasVisitas() {
			$res = sql_select('E.PROG_NOMBRE, 
		  MONTH(R.fecha_creacion) AS mes,
		  determinar_turno(TIME(R.fecha_creacion)) AS turno_tipo,
		  COUNT(*) AS cantidad', "upc_libros_visita AS R 
		INNER JOIN upc_estudiantes AS E 
		  ON R.identificacion = E.PEGE_DOCUMENTOIDENTIDAD", "R.statut = 'Activo' AND E.PROG_NOMBRE IS NOT NULL AND E.PROG_NOMBRE != ''
		GROUP BY E.PROG_NOMBRE, MONTH(R.fecha_creacion), determinar_turno(TIME(R.fecha_creacion))");
			$data = [];
			while ($r = sql_fetch($res)) {
				$data[] = $r;
			}
			return $data;
		}

		private function getDataVisitas($campo) {
			$res = sql_select('MONTH(fecha_creacion) AS mes,
				DAY(fecha_creacion) AS dia,
				SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) AS total_mañana,
				SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) AS total_tarde,
				SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) AS total_nocturna,
				COUNT(*) AS total_registros,
				ROUND(SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_mañana,
				ROUND(SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_tarde,
				ROUND(SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_nocturna',
				'upc_libros_visita GROUP BY MONTH(fecha_creacion), DAY(fecha_creacion)',
				'');
			
			$data = array();
			$map = array(
				'total_mañana' => 'total_mañana',
				'total_tarde' => 'total_tarde',
				'total_nocturna' => 'total_nocturna'
			);
			
			while ($row = sql_fetch($res)) {
				$data[] = intval($row[$map[$campo]]);
			}
			return $data;
		}
		private function getTotalRegistrosTurnoVisitas($campo) {
			$row = sql_fetsel('"Totales" AS descripcion,
				  SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) AS total_mañana,
				  SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) AS total_tarde,
				  SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) AS total_nocturna,
				  COUNT(*) AS total_registros,
				  ROUND(SUM(CASE WHEN jornada = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_mañana,
				  ROUND(SUM(CASE WHEN jornada = 2 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_tarde,
				  ROUND(SUM(CASE WHEN jornada = 3 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS porcentaje_nocturna','upc_libros_visita','');
			
			$total=array('total_mañana'=>$row['total_mañana'],'total_tarde'=>$row['total_tarde'],'total_nocturna'=>$row['total_nocturna']);
			
			switch ($campo) {
				case 'total_mañana':
					return $total['total_mañana'];
				case 'total_tarde':
					return $total['total_tarde'];
				case 'total_nocturna':
					return $total['total_nocturna'];
				default:
					return 0;
			}
		}

		private function calcularPorcentajeTurnoVisitas($totalRegistros, $campo) {
			$totalRegistrosTurno = $this->getTotalRegistrosTurnoVisitas($campo);
			if ($totalRegistros == 0) {
				return 0;
			}
			return round(($totalRegistrosTurno / $totalRegistros) * 100, 3);
		}
}		