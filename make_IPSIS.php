<?php
/*****************************************************************
 * This script creates CSV files for use with the IPSIS batch creator.
 * IPSIS specifications are outlined at https://community.d2l.com/brightspace/kb/articles/5972-prepare-csv-files-using-the-d2l-standard-csv-format
 * 
 * This script is intended to be run from the command line. ie. php make_IPSIS.php
 * 
 * This script is provided as-is and is not supported by D2L. It is intended for generating a reference, basic Org Structure.
 * 
 * Additional scenarios can be added by defining new properties to within the switch statements. The version_compare allows for a scenario 1.1 to rely on the same code as 1.0.
 * 
 * Created by: Matt Clare
 * Date: Tuesday September 12, 2023
 *****************************************************************/
// Define the common suffix variable
$commonSuffix = "_TEST";
$version=2.1; //Value written to manifest.json. Mainly affects Other and User file, ie. Adds preferred names to Users file.
$zipFileName = 'TEST-IPSIS-Batch-Creator'.$commonSuffix.'-'.date("Y-m-d-His").'.zip'; // Specify the name of the zip file

$scenario = 1.1;

if (version_compare($scenario,2,"<")) { //Scenario 1 basic data
    $defaultAction = "UPDATE";
    $startDate = date('Y-m-d', strtotime('-1 day'));
    $endDate = date('Y-m-d', strtotime('+90 days'));
    $offeringCode = 'TEST-OFFERING';
    $semesterCode = 'TEST-SEMESTER';
}
// Create an array to store the paths of the created CSV files
$csvFilePaths = array();

// Create an array of file names
$fileNames = array(
    "0-Other",
    "1-Departments",
    "2-Semesters",
    "3-Templates",
    "4-Offerings",
    "5-Sections",
    "6-Users",
    "7-Enrollments"
);

// Define a mapping of headers for each file name
$headerMapping = array(
    //type,action,code,name,start_date,end_date,is_active,department_code,template_code,semester_code,offering_code,custom_code
    "0-Other" =>       array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "1-Departments" => array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "2-Semesters" =>   array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "3-Templates" =>   array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "4-Offerings" =>   array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "5-Sections" =>    array('type','action','code','name','start_date','end_date','is_active','department_code','template_code','semester_code','offering_code','custom_code'),
    "6-Users" =>       array('type','action','username','org_defined_id','first_name','last_name','password','is_active','role_name','email','relationships','pref_first_name','pref_last_name','sort_last_name'),
    "7-Enrollments" => array('type','action','child_code','role_name','parent_code')
);

// Loop through the array and create CSV files
foreach ($fileNames as $fileName) {
    // Get the header for this file
    $header = $headerMapping[$fileName];

    // Get the data for this file (replace with your actual data)

    $dataForFile = array();

    switch ($fileName) {
        case "0-Other":
            if (version_compare($scenario,2,"<")) $dataForFile = array('faculty',$defaultAction,'TEST-FACULTY','TEST FACULTY');
            break;
        case "1-Departments":
            if (version_compare($scenario,2,"<")) $dataForFile = array('department',$defaultAction,'TEST-DEPARTMENT','TEST DEPARTMENT','','','TRUE','','','','','TEST-FACULTY');
            break;
        case "2-Semesters":
            //semester,UPDATE,OTHR-HS,NonAcademic,2020-01-01,2999-12-31,,,,,,

            if (version_compare($scenario,2,"<")) {
                $startDate = date('Y-m-d', strtotime('-1 months'));
                $endDate = date('Y-m-d', strtotime('+3 months'));
                $dataForFile = array('semester',$defaultAction,$semesterCode,'TEST',$startDate,$endDate);
            }
            break;
        case "3-Templates":
            if (version_compare($scenario,2,"<")) $dataForFile = array('course template',$defaultAction,'TEST-TEMPLATE','TEST TEMPLATE','','','','TEST-DEPARTMENT');
            break;
        case "4-Offerings":
            if (version_compare($scenario,2,"<")) {
                $dataForFile = array('course offering',$defaultAction,$offeringCode,'TEST OFFERING',$startDate,$endDate,'','TEST-DEPARTMENT','TEST-TEMPLATE',$semesterCode);
            }
            break;
        case "5-Sections":
            if (version_compare($scenario,2,"<")) {
                $dataForFile = array('<!-- Use sectionData! This is just to trigger the fputscsv logic-->');
                $sectionData = array();
                for($i=1;$i<=10;$i++) {
                    $sectionData[] = array('course section',$defaultAction,sprintf('TEST-COURSE-SECTION-%\'.02d',$i),sprintf('TEST COURSE SECTION %\'.02d',$i),'','','','','','',$offeringCode,"");
                }
            }
            break;
        case "6-Users":
            if (version_compare($scenario,2,"<")) {       
                $dataForFile = array('<!-- Use sectionData! This is just to trigger the fputscsv logic-->');
                $org_defined_id = 90568855500; //Starting point for org_defined_ids, also Brock University's phone number
                $userData = array();
                for($i=1;$i<=10;$i++) {
                    $org_defined_id++;
                    $userData[] = array('user',$defaultAction,sprintf('test_user_%\'.03d',$i),$org_defined_id,sprintf('TEST USER FIRST %\'.03d',$i),sprintf('TEST USER LAST %\'.03d',$i),'','TRUE','student',sprintf('test%\'.03d@localhost.localdomain',$i),'',sprintf('FIRST %\'.03d',$i),sprintf('LAST %\'.03d',$i),"");
                }
            }
            break;
        case "7-Enrollments":
            if (version_compare($scenario,1,"==")) {       
                $dataForFile = array('<!-- Use sectionData! This is just to trigger the fputscsv logic-->');
                $enrollmentsData = array();
                foreach($userData as $value) {
                    $enrollmentsData[] = array('enrollment',$defaultAction,$value[3],'Student','TEST-COURSE-SECTION-01');
                }
            }
            if (version_compare($scenario,1.1,"==")) {       
                $dataForFile = array('<!-- Use sectionData! This is just to trigger the fputscsv logic-->');
                $enrollmentsData = array();
                for($i=1;$i<=1000;$i++) {
                     // Determine the action based on even and odd loop iterations
                    $action = ($i % 2 == 0) ? 'UPDATE' : 'DELETE';
                    $enrollmentsData[] = array('enrollment',$action,$userData[0][3],'Student','TEST-COURSE-SECTION-01');
                }

            }
            break;
    }

    // Write the data to the CSV file, but only if we have data to write
    if (count($dataForFile) > 0) {

        // Pad the data array if its length is less than the header's length
        if (count($dataForFile) > count($header)) die("The data array is longer than the header array.");
        else if (count($dataForFile) < count($header)) {
            $dataForFile = array_pad($dataForFile, count($header), '');
        }

        // Generate the file name with the common suffix
        $csvFileName = $fileName . $commonSuffix .'-' .date('YmdHis').".csv";
        $csvFilePaths[] = $csvFileName; // Store the path of the CSV file
        
        // Open the CSV file for writing
        $file = fopen($csvFileName, 'w');

        // Write the header to the CSV file
        fputcsv($file, $header);

        switch ($fileName) {
            case "5-Sections":
                foreach ($sectionData as $value) {
                    fputcsv($file, $value);
                }
            break;
            case "6-Users":
                foreach ($userData as $value) {
                    fputcsv($file, $value);
                }
            break;
            case "7-Enrollments":
                foreach ($enrollmentsData as $value) {
                    fputcsv($file, $value);
                }
            break;
            default:
                fputcsv($file, $dataForFile);
            break;
         }
       

        // Close the CSV file
        fclose($file);
    }
}
//Create manifest.json file
if ($version > 1) {
    $file = fopen('manifest.json', 'w');
    fputs($file, '{"version":"'.$version.'"}');
    fclose($file);
} 

echo "CSV files created successfully.\n";

// Create a zip archive containing the CSV files
$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
    foreach ($csvFilePaths as $csvFilePath) {
        $zip->addFile($csvFilePath, basename($csvFilePath));
    }
    $zip->addFile('manifest.json', basename('manifest.json'));
    $zip->close();
echo "CSV files zipped successfully. Zip file: '$zipFileName'\n";
} else {
    echo "Failed to create the zip file.\n";
}

?>
