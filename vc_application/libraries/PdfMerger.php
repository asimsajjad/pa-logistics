<?php
use setasign\Fpdi\Fpdi;

class PdfMerger {

    public function __construct() {
        // Load FPDI
        require_once APPPATH . 'third_party/fpdf/vendor/autoload.php';
        require_once APPPATH . 'third_party/fpdi/vendor/autoload.php';
    }

    public function merge_pdfs_backup($files = array(), $file_name, $output = 'I') {
        if (empty($files)) {
            return false;
        }

        // Create new FPDI object
        $pdf = new Fpdi();

        // Iterate through files and add each page to the output PDF
        foreach ($files as $file) {
            // Get the number of pages in the file
            $pageCount = $pdf->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                // Import each page
                $tplIdx = $pdf->importPage($i);
                
                // Get the size of the imported page
                $size = $pdf->getTemplateSize($tplIdx);

                // Set the orientation based on the width and height
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                // Add a new page with the correct orientation and size
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                
                // Use the imported page as a template
                $pdf->useTemplate($tplIdx);
            }
        }
        // Iterate through files and add each page to the output PDF
        /*foreach ($files as $file) {
            // Get the number of pages in the file
            $pageCount = $pdf->setSourceFile($file);
            // Add each page
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx);
            }
        }*/

        // Output the merged PDF (I = inline in browser, D = download, F = file)
        $pdf->Output($file_name, $output);
    }

    public function merge_pdfs($files = array(), $file_name, $output = 'I') {
        if (empty($files)) {
            log_message('error', 'No files provided for PDF merge');
            return false;
        }

        $pdf = new Fpdi();
        foreach ($files as $file) {
            try {
                $pageCount = $pdf->setSourceFile($file);
                if ($pageCount === false) {
                    log_message('error', 'Failed to read PDF: ' . $file);
                    continue;
                }
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplIdx);

                    // Define A4 dimensions (mm)
                    $a4Width = 210; // A4 portrait width
                    $a4Height = 297; // A4 portrait height
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                    // Scale if width or height exceeds A4
                    $maxWidth = $orientation === 'P' ? $a4Width : $a4Height;
                    $maxHeight = $orientation === 'P' ? $a4Height : $a4Width;
                    $scale = min($maxWidth / $size['width'], $maxHeight / $size['height'], 1); // Scale down if needed, preserve aspect ratio
                    $newWidth = $size['width'] * $scale;
                    $newHeight = $size['height'] * $scale;

                    // Center the page on A4
                    $x = ($a4Width - $newWidth) / 2;
                    $y = ($a4Height - $newHeight) / 2;

                    $pdf->AddPage($orientation, [$a4Width, $a4Height]);
                    $pdf->useTemplate($tplIdx, $x, $y, $newWidth, $newHeight);
                }
            } catch (Exception $e) {
                log_message('error', 'Error merging PDF ' . $file . ': ' . $e->getMessage());
                continue;
            }
        }
        try {
            $pdf->Output($file_name, $output);
        } catch (Exception $e) {
            log_message('error', 'Error outputting PDF: ' . $e->getMessage());
            return false;
        }
    }

    public function download($outputFileName = 'combined.pdf')
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        $this->pdf->Output('D', $outputFileName);
    }
    public function save($outputPath = 'combined.pdf')
    {
        $this->pdf->Output('F', $outputPath); // 'F' means save to file
    }
}
