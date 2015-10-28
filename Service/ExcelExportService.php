<?php

namespace Flower\ClientsBundle\Service;

use PHPExcel;
use PHPExcel_IOFactory;

/**
 * Description of ExcelExportService
 *
 * @author Pedro Barri <pbarri@flowcode.com.ar>
 */
class ExcelExportService
{

    /**
     * ExportAll() genera el archivo excel para exportar todo el contenido.
     *
     */
    public function exportAll()
    {
    	// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("PHPExcel Test Document")
									 ->setSubject("PHPExcel Test Document")
									 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
									 ->setKeywords("office PHPExcel php")
									 ->setCategory("Test result file");
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Hello')
		            ->setCellValue('B2', 'world!')
		            ->setCellValue('C1', 'Hello')
		            ->setCellValue('D2', 'world!');
		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
		$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
    }
}