<?php

namespace Flower\ClientsBundle\Service;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

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
		$objPHPExcel = $this->container->get('phpexcel')->createPHPExcelObject();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Flower")
									 ->setTitle($title)
									 ->setSubject("PHPExcel Test Document");
		if($description){
			$objPHPExcel->getProperties()->setDescription($description);
		}
		//setCellValueByColumnAndRow (columna, fila, valor)
		$row = 1;
		$column = 0;
		foreach ($data as $rowData) {
			$column = 0;
			if($rowData && is_array($rowData) && array_key_exists("values" ,$rowData)){
				
				foreach ($rowData["values"] as  $item) {
					if($item){
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($column, $row, $item);
						$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
						if(array_key_exists("styles" ,$rowData) && is_array($rowData["styles"])){
							$objPHPExcel->setActiveSheetIndex(0)->getStyle("$columnName$row")->applyFromArray(
							    $rowData["styles"]
							);
						}

						$column++;
					}
				}
			}
			$row++;
		}
		for ($i=0; $i < $column; $i++) { 
			$objPHPExcel->getActiveSheet()->getColumnDimension( PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
		}
		
		if($row == 1){
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(0, 1, "no hay datos");
		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//TODO: Acomodar RUTA
		$webdir = $this->container->getParameter('kernel.root_dir') . "/../web";

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$title.'.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter->save( 'php://output');
    }
}