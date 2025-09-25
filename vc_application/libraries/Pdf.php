<?php
class Pdf {

    /*public function __construct() {
        $CI = &get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }*/

    public function load($params = []) {
        // Autoload mPDF via Composer
        //require_once APPPATH . '../vendor/autoload.php';
        require_once APPPATH .'third_party/mpdf-new/vendor/autoload.php';

        // Default parameters for mPDF
        if (empty($params)) {
            $params = [
                'mode' => 'en-GB-x',  // Language and region
                'format' => 'A4',    // Paper size
                'margin_left' => 10, // Left margin
                'margin_right' => 10,// Right margin
                'margin_top' => 10,  // Top margin
                'margin_bottom' => 10,// Bottom margin
                'margin_header' => 6,// Header margin
                'margin_footer' => 3 // Footer margin
            ];
        }

        // Initialize mPDF with parameters
        return new \Mpdf\Mpdf($params);
    }
}

class PdfOld {

    function Pdf() {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }

    function load($param = NULL) {
        require_once APPPATH .'third_party/mpdf/mpdf.php';

        if ($param == NULL) {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        }
        return new mPDF($param);
    }
}
