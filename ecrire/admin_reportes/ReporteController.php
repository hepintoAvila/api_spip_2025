<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	 
	include_spip('ecrire/admin_reportes/reporteService');
	include_spip('ecrire/admin_reportes/VisitaService');
	include_spip('ecrire/admin_reportes/historicoService');
class ReporteController {
	
    private $reporteService;
    private $visitaService;

    public function __construct() {
        $this->reporteService = new ReporteService();
        $this->visitaService = new VisitaService();
        $this->historicoService = new HistoricoService();
    }

	public function getChartWidget() {
		$datosWidget = $this->reporteService->getChartWidget();
		$libroVisitas = $this->visitaService->getChartWidgetVisitas();
		
		//print_r($libroVisitas);
		if (is_array($datosWidget) && is_array($libroVisitas)) {
			$result = array_merge($datosWidget, $libroVisitas);
		} else {
			$chartwidget['chartwidget'] = array();
			$libroVisitas['libroVisitas'] = array();
			$result = array_merge($chartwidget, $libroVisitas);
			 
		}
		
		if (!empty($result['chartwidget']) || !empty($result['libroVisitas'])) {
			
			$var = var2js(['status' => 200, 'type' => 'success', 'data' => $result, 'message' => 'Listado de chartwidget']);
			echo $var;
		} else {
			$records = ['status' => 404, 'type' => 'error', 'data' => [], 'message' => 'No existen registros de chartwidget'];
			$var = var2js($records);
			echo $var;
		}
	}
		public function getHistoricoPrograma($data) {
		$result = $this->historicoService->getHistoricos($data);
		
		if (!empty($result['Historicos'])) {
			$var = var2js(['status' => 200, 'type' => 'success', 'data' => $result, 'message' => 'Listado de Historicos']);
			echo $var;
		} else {
			$records = ['status' => 404, 'type' => 'error', 'data' => [], 'message' => 'No existen registros de Historicos'];
			$var = var2js($records);
			echo $var;
		}
	}
}
	