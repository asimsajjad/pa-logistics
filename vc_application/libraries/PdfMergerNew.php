<?php
use Imagick;

class PdfMergerNew {

    public function __construct() {
        // Include manually installed mPDF
        //require_once APPPATH . 'third_party/mpdf/mpdf.php';
        require_once APPPATH .'third_party/mpdf-new/vendor/autoload.php';
    }

    // Convert PDF pages to images and get their dimensions
    public function convert_pdf_to_images($pdf_file) {
        $images = [];
        try {
            $imagick = new Imagick();
            
            // Set resolution to improve quality
            $imagick->setResolution(250, 250);
    
            $imagick->readImage($pdf_file);
            $pageCount = $imagick->getNumberImages();
    
            // Iterate through each page
            for ($i = 0; $i < $pageCount; $i++) {
                $imagick->setIteratorIndex($i);
                $imagick->setImageFormat('jpg'); 
    
                // Optional: Set high image quality
                $imagick->setImageCompressionQuality(100); 
    
                $outputFile = FCPATH . 'assets/pdf_page_' . uniqid() . '_page_' . $i . '.jpg';
                $imagick->writeImage($outputFile);
    
                // Get dimensions of the page
                $width = $imagick->getImageWidth();
                $height = $imagick->getImageHeight();
    
                $images[] = [
                    'file' => $outputFile,
                    'width' => $width,
                    'height' => $height
                ];
            }
    
            // Clear resources
            $imagick->clear();
        } catch (Exception $e) {
            log_message('error', 'Error converting PDF to images: ' . $e->getMessage());
        }
    
        return $images;
    }
    
    // Combine images into a new PDF using mPDF
    public function combine_images_to_pdf_backup($images = [], $output = 'I',$pdfName) {
        if (empty($images)) {
            return false; // No images to combine
        }

        // Initialize mPDF
        //$mpdf = new \mPDF();
        $mpdf = new \Mpdf\Mpdf();

        foreach ($images as $image) {
            // Calculate page size in mm (convert from pixels; assuming 72 dpi)
            $width_mm = ($image['width'] / 72) * 25.4;
            $height_mm = ($image['height'] / 72) * 25.4;
            // Determine orientation
            $orientation = $width_mm > $height_mm ? 'L' : 'P'; // Landscape or Portrait
            // Set page size dynamically
            $mpdf->_setPageSize([$width_mm, $height_mm],$orientation); // Custom size in mm
            $mpdf->AddPage();

            // Add the image to the page
            $mpdf->Image($image['file'], 0, 0, $width_mm, $height_mm);
        }

        // Output the combined PDF
        $mpdf->Output($pdfName, $output); // I = inline, D = download, F = file
    }

    public function combine_images_to_pdf($images = [], $output = 'I', $pdfName) {
        if (empty($images)) {
            return false; // No images to combine
        }
    
        // Define a standard page size (e.g., A4 dimensions in mm: 210 x 297)
        $standardWidth = 210; // in mm
        $standardHeight = 297; // in mm
        $dpi = 72; // Assumed DPI
    
        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'format' => [$standardWidth, $standardHeight], // Set page size globally
        ]);
    
        foreach ($images as $image) {
            // Get the original dimensions of the image
            $originalWidthMm = ($image['width'] / $dpi) * 25.4;
            $originalHeightMm = ($image['height'] / $dpi) * 25.4;
    
            // Calculate scaling factor to fit the image within the standard page size
            $scale = min($standardWidth / $originalWidthMm, $standardHeight / $originalHeightMm);
    
            // Calculate resized dimensions
            $resizedWidth = $originalWidthMm * $scale;
            $resizedHeight = $originalHeightMm * $scale;
    
            // Calculate position to center the image on the page
            $x = ($standardWidth - $resizedWidth) / 2;
            $y = ($standardHeight - $resizedHeight) / 2;
    
            // Add a new page with the standard size
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 0, 0, $standardWidth, $standardHeight);
    
            // Add the resized image centered on the page
            $mpdf->Image($image['file'], $x, $y, $resizedWidth, $resizedHeight);
        }
    
        // Output the combined PDF
        $mpdf->Output($pdfName, $output); // I = inline, D = download, F = file
    }
    
    // Combine PDFs using image-rendering method
    public function merge_pdfs_with_images($pdf_files = [], $pdfName = 'combined_pdf.pdf', $output = 'I') {
        $all_images = [];

        foreach ($pdf_files as $pdf_file) {
            // Convert each PDF to images
            $images = $this->convert_pdf_to_images($pdf_file);
            $all_images = array_merge($all_images, $images);
        }

        // Combine all images into a single PDF
        $this->combine_images_to_pdf($all_images, $output,$pdfName);
    }
}
