<?php
use setasign\Fpdi\Fpdi;

class PdfMergerNewNew
{
    private $pdf;

    public function __construct()
    {
        $this->pdf = new Fpdi();
    }

    // public function addPdf($pdfFiles, $imageResolution = 72)
    // {
    //     foreach ($pdfFiles as $index => $file) {
    //         if ($index === 0) {
    //             // Include the first PDF directly
    //             $this->addPdfDirect($file);
    //         } else {
    //             // Convert subsequent PDFs to images and add them
    //             $this->addPdfAsImage($file, $imageResolution);
    //         }
    //     }
    // }

    public function addPdf($pdfFiles, $imageResolution = 100, $maxPages = 30) {
    foreach ($pdfFiles as $index => $file) {
        log_message('debug', 'Processing file: ' . $file);
        if ($index === 0) {
            $this->addPdfDirect($file);
        } else {
            $this->addPdfAsImage($file, $imageResolution, $maxPages);
        }
    }
}

    private function addPdfDirect($file)
    {
        $pageCount = $this->pdf->setSourceFile($file);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl = $this->pdf->importPage($i);
            $size = $this->pdf->getTemplateSize($tpl);
            $this->pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $this->pdf->useTemplate($tpl);
        }
    }

    private function addPdfAsImage_backup($file, $imageResolution)
    {
        $imagick = new Imagick();

        try {
            $imagick->setResolution($imageResolution, $imageResolution); // Set resolution
            $imagick->readImage($file);

            foreach ($imagick as $page) {
                $imagePath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
                $page->setImageFormat('jpg');
                $page->setImageCompressionQuality(90); // Adjust quality if needed
                $page->writeImage($imagePath);

                // Determine orientation
                $orientation = ($page->getImageWidth() > $page->getImageHeight()) ? 'L' : 'P';

                // Add the image to the PDF
                $this->pdf->AddPage($orientation);
                $this->pdf->Image($imagePath, 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());

                // Clean up temporary file
                unlink($imagePath);
            }
        } catch (Exception $e) {
            throw new Exception("Error processing PDF as image: " . $e->getMessage());
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }
    // private function addPdfAsImage($file, $imageResolution)
    // {
    //     $imagick = new Imagick();

    //     try {
    //         $imagick->setResolution($imageResolution, $imageResolution);
    //         $imagick->readImage($file);
    //     } catch (\Exception $e) {
    //         return; // Skip bad file
    //     }

    //     foreach ($imagick as $page) {
    //         try {
    //             $imagePath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';

    //             // Convert page to PNG first to preserve transparency
    //             $page->setImageFormat('png');

    //             // Create white canvas and draw the page on it
    //             $canvas = new Imagick();
    //             $canvas->newImage($page->getImageWidth(), $page->getImageHeight(), new ImagickPixel('white'));
    //             $canvas->compositeImage($page, Imagick::COMPOSITE_OVER, 0, 0);

    //             // Export as JPG
    //             $canvas->setImageFormat('jpg');
    //             $canvas->setImageCompressionQuality(90);
    //             $canvas->writeImage($imagePath);

    //             // Set orientation for the PDF page
    //             $orientation = ($canvas->getImageWidth() > $canvas->getImageHeight()) ? 'L' : 'P';

    //             $this->pdf->AddPage($orientation);
    //             $this->pdf->Image($imagePath, 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());

    //             unlink($imagePath);
    //             $canvas->clear();
    //             $canvas->destroy();
    //         } catch (\Exception $e) {
    //             continue;
    //         }
    //     }

    //     $imagick->clear();
    //     $imagick->destroy();
    // }

    private function addPdfAsImage($file, $imageResolution = 100, $maxPages = 30) {
        $imagick = new Imagick();
        try {
            // Optimize Imagick settings
            $imagick->setResolution($imageResolution, $imageResolution);
            $imagick->setOption('pdf:use-cropbox', 'true'); // Use PDF crop box to reduce unnecessary content
            $imagick->readImage($file . '[0-' . ($maxPages - 1) . ']'); // Limit to maxPages
        } catch (\Exception $e) {
            log_message('error', 'Failed to read PDF: ' . $file . ' - ' . $e->getMessage());
            return; // Skip bad file
        }

        foreach ($imagick as $index => $page) {
            try {
                $imagePath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';

                // Create white canvas to handle transparency
                $canvas = new Imagick();
                $canvas->newImage($page->getImageWidth(), $page->getImageHeight(), new ImagickPixel('white'));
                $canvas->compositeImage($page, Imagick::COMPOSITE_OVER, 0, 0);

                // Optimize JPG output
                $canvas->setImageFormat('jpg');
                $canvas->setImageCompressionQuality(85); // Higher quality for better visuals
                $canvas->stripImage(); // Remove metadata to reduce size
                $canvas->writeImage($imagePath);

                // Log image size for debugging
                log_message('debug', 'Generated image ' . $imagePath . ' for page ' . ($index + 1) . ' size: ' . filesize($imagePath) . ' bytes');

                $orientation = ($canvas->getImageWidth() > $canvas->getImageHeight()) ? 'L' : 'P';
                $this->pdf->AddPage($orientation);
                $this->pdf->Image($imagePath, 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());

                unlink($imagePath);
                $canvas->clear();
                $canvas->destroy();
            } catch (\Exception $e) {
                log_message('error', 'Error processing page ' . ($index + 1) . ' in ' . $file . ': ' . $e->getMessage());
                continue;
            }
        }

        $imagick->clear();
        $imagick->destroy();
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

/*
use setasign\Fpdi\Fpdi;

class PdfMergerNewNew
{
    private $pdf;

    public function __construct()
    {
        $this->pdf = new Fpdi();
    }

    public function addPdf($pdfFiles)
    {
        foreach ($pdfFiles as $index => $file) {
            if ($index === 0) {
                // Include the first PDF directly
                $this->addPdfDirect($file);
            } else {
                // Convert subsequent PDFs to images and add them
                $this->addPdfAsImage($file);
            }
        }
    }

    private function addPdfDirect($file)
    {
        $pageCount = $this->pdf->setSourceFile($file);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl = $this->pdf->importPage($i);
            $size = $this->pdf->getTemplateSize($tpl);
            $this->pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $this->pdf->useTemplate($tpl);
        }
    }

    private function addPdfAsImage($file)
    {
        $imagick = new Imagick();

        try {
            $imagick->readImage($file);

            foreach ($imagick as $page) {
                $imagePath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
                $page->setImageFormat('jpg');
                $page->writeImage($imagePath);

                // Add the image to the PDF
                $this->pdf->AddPage();
                $this->pdf->Image($imagePath, 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());

                // Clean up temporary file
                unlink($imagePath);
            }
        } catch (Exception $e) {
            throw new Exception("Error processing PDF as image: " . $e->getMessage());
        } finally {
            $imagick->clear();
            $imagick->destroy();
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
}

// Example Usage
/*$pdfMerger = new PdfMergerNew();
$pdfFiles = [
    'path/to/first.pdf', // This will be added directly
    'path/to/second.pdf', // This will be converted to images
    'path/to/third.pdf'   // This will also be converted to images
];
$pdfMerger->addPdf($pdfFiles);
$pdfMerger->download('combined_output.pdf');
*/