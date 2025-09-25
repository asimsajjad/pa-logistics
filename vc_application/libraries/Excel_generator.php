<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'third_party/vendor/autoload.php'; // Path to the PhpSpreadsheet autoload file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel_generator {
    public function generateExcel($data, $filename) {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write data to the Excel file
        foreach ($data as $rowIndex => $rowData) {
            foreach ($rowData as $columnIndex => $value) {
                $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $value);
            }
        }

        // Create a writer object
        $writer = new Xlsx($spreadsheet);

        // Save the Excel file to a temporary directory
        $filePath = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($filePath);

        // Set headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Output the file to the browser
        readfile($filePath);

        // Delete the temporary file
        unlink($filePath);

        exit;
    }
}