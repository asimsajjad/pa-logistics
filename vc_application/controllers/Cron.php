<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {
    //wget -O /dev/null https://dispatch.patransportca.com/Cron/downloadDbBackup
	public	 function __construct()
	{
		parent::__construct();
		//$this->load->library('session');
		$this->load->helper('url');
		//$this->load->library('form_validation');
		//$this->load->model('Comancontroler_model'); 
	}
	
	public function downloadDbBackup() {
        
        $this->load->dbutil();
        $this->load->helper('file');
        //$this->load->helper('url');
        
        // Database backup preferences
        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'backup-on-'.date('Y-m-d').'.sql'
        );

        // Create the database backup
        $backup = $this->dbutil->backup($prefs);

        // Set the backup file name with the current date
        $db_name = 'backup-on-'.date('Y-m-d').'.zip';

        // Set the path to save the backup file
        $save_path = FCPATH . 'backup/' . $db_name;

        // Write the backup file to the specified path
        if (write_file($save_path, $backup)) {
            echo "Backup completed successfully! File saved to: " . $save_path;
        } else {
            echo "Failed to write backup file.";
        }
        
        // Zip a specific folder
        $this->zip_folder('/home3/patransport/public_html/dispatch/vc_application', 'backup-application-' . date('Y-m-d') . '.zip');
  
    }
    
    private function zip_folder($folder_path, $zip_name) {
        // Read the folder and add its files to the zip archive
        $this->zip->read_dir($folder_path, FALSE);

        // Set the path to save the zip file
        $zip_path = FCPATH . 'backup/' . $zip_name;

        // Write the zip file to the specified path
        if ($this->zip->archive($zip_path)) {
            echo "Folder backup completed successfully! File saved to: " . $zip_path;
        } else {
            echo "Failed to write folder backup file.";
        }

        // Clear the zip data to free memory
        $this->zip->clear_data();
    }
    
}