<?php
$host= "localhost";
$user= "root";
$password = '';
$database= 'pegasus_socks_live';
date_default_timezone_set("Asia/Dhaka");

$conn  = mysqli_connect($host, $user, $password, $database);

if($conn->connect_error){
    die("connection fail ".$conn->connect_error);
}

    $conn->set_charset("utf8");


    // Get All Table Names From the Database
    $tables = array();
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    foreach ($tables as $table) {
        
        // Prepare SQLscript for creating table structure
        $query = "SHOW CREATE TABLE $table";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";
        
        
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
        
        $columnCount = mysqli_num_fields($result);
        
        // Prepare SQLscript for dumping data for each table
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"';
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        
        $sqlScript .= "\n"; 
    }

    if(!empty($sqlScript))
    {

        if(!file_exists('E:/Database-backup')){
            mkdir("E:/Database-backup");
        }

        $dir = "E:/Database-backup/";

        // Save the SQL script to a backup file
        $backup_file_name = $database . '_' . date('Y-m-d').time() . '.sql';
        $fileHandler = fopen($dir.$backup_file_name, 'w+');
        $number_of_lines = fwrite($fileHandler, $sqlScript);
        fclose($fileHandler); 

        
        $zipname = date('Y-m-d')."-".time().".zip";
        $zip_dir = $dir.$zipname;
        $zip = new ZipArchive;
        $zip->open($zip_dir, ZipArchive::CREATE);
        $zip->addFile($dir.$backup_file_name, $backup_file_name);
        $zip->close();

        unlink($dir.$backup_file_name);
        

        // Download the SQL backup file to the browser
       /*  header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file_name));
        ob_clean();
        flush();
        readfile($backup_file_name);
        exec('rm ' .$backup_file_name); */ 
    }




?>