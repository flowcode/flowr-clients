<?php

namespace Flower\ClientsBundle\Service;

use PHPExcel;
use PHPExcel_IOFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Description of ExcelExportService
 *
 * @author Pedro Barri <pbarri@flowcode.com.ar>
 */
class ExcelExportService
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
    }

    /**
     * ExportAll() genera el archivo excel para exportar todo el contenido.
     *
     */
    public function exportData($data,$title, $description = null)
    {
    	// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Flower")
									 ->setTitle($title)
									 ->setSubject("PHPExcel Test Document");
		if($description){
			$objPHPExcel->getProperties()->setDescription($description);
		}

		//setCellValueByColumnAndRow (columna, fila, valor)
		$row = 1;
		foreach ($data as $rowData) {
			$column = 0;
			foreach ($rowData as $item) {
				if($item){
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($column, $row, $item);
					$column++;
				}
			}
			$row++;
		}
        
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//TODO: Acomodar RUTA
		$webdir = $this->container->getParameter('kernel.root_dir') . "/../web";
		$completeFileName = $webdir.'/account.xlsx';

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter->save( 'php://output');
    }
}