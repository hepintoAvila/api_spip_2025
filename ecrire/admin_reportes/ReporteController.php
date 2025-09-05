<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	 
	include_spip('ecrire/admin_reportes/reporteService');
	include_spip('ecrire/admin_reportes/VisitaService');
class ReporteController {
	
    private $reporteService;
    private $visitaService;

    public function __construct() {
        $this->reporteService = new ReporteService();
        $this->visitaService = new VisitaService();
    }

	public function getChartWidget() {
		$datosWidget = $this->reporteService->getChartWidget();
		$libroVisitas = $this->visitaService->getChartWidgetVisitas();
		
		if (is_array($datosWidget) && is_array($libroVisitas)) {
			$result = array_merge($datosWidget, $libroVisitas);
		} else {
			$result = array();
			if (is_array($datosWidget)) {
				$result = $datosWidget;
			}
			if (is_array($libroVisitas)) {
				$result = array_merge($result, $libroVisitas);
			}
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
}
	