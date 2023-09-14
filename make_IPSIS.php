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
 * Date: Thursday September 14, 2023
 *****************************************************************/
// Define the common suffix variable
$commonSuffix = "_TEST";
$version=2.1; //Value written to manifest.json. Mainly affects Other and User file, ie. Adds preferred names to Users file.
$zipFileName = 'TEST-IPSIS-Batch-Creator'.$commonSuffix.'-'.date("Y-m-d-His").'.zip'; // Specify the name of the zip file

$scenario = 1.1;

/*
Scenario 1.0: Create a basic Org Structure from Faculty (Other, between Root/Org and Department) to Course Section (Child, between Offering and User)
Section 1.1-1.5: Enroll users in different sections, different amounts of times first section
Scenario 1.4 should be that test_user_10 is enrolled in TEST-COURSE-SECTION-07 in batch 1.41, then added to TEST-COURSE-SECTION-05 in batch 1.42, then removed from TEST-COURSE-SECTION-07 in batch 1.42

*/
if (version_compare($scenario,2,"<")) { //Scenario 1 basic data
    $defaultAction = "UPDATE";
    $startDate = date('Y-m-d', strtotime('-1 day'));
    $endDate = date('Y-m-d', strtotime('+90 days'));
    $offeringCode = 'TEST-OFFERING';
    $semesterCode = 'TEST-SEMESTER';
    $org_defined_id = 90568855500; //Starting point for org_defined_ids, also Brock University's phone number
    $totalUsers = 10;
    $sectionsPerOffering = 10;
}
if (version_compare($scenario,1.1,"==")) $totalEnrollmentActions = 1000;

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
            if (version_compare($scenario,3,"<")) $dataForFile[] = array('faculty',$defaultAction,'TEST-FACULTY','TEST FACULTY');
            break;
        case "1-Departments":
            if (version_compare($scenario,2,"<")) $dataForFile[] = array('department',$defaultAction,'TEST-DEPARTMENT','TEST DEPARTMENT','','','TRUE','','','','','TEST-FACULTY');
            break;
        case "2-Semesters":
            if (version_compare($scenario,2,"<")) {
                $startDate = date('Y-m-d', strtotime('-1 months'));
                $endDate = date('Y-m-d', strtotime('+3 months'));
                $dataForFile[] = array('semester',$defaultAction,$semesterCode,'TEST',$startDate,$endDate);
            }
            break;
        case "3-Templates":
            if (version_compare($scenario,2,"<")) $dataForFile[] = array('course template',$defaultAction,'TEST-TEMPLATE','TEST TEMPLATE','','','','TEST-DEPARTMENT');
            break;
        case "4-Offerings":
            if (version_compare($scenario,2,"<")) {
                $dataForFile[] = array('course offering',$defaultAction,$offeringCode,'TEST OFFERING',$startDate,$endDate,'','TEST-DEPARTMENT','TEST-TEMPLATE',$semesterCode);
            }
            break;
        case "5-Sections":
            if (version_compare($scenario,2,"<")) {
                for($i=1;$i<=$sectionsPerOffering;$i++) {
                    $dataForFile[] = array('course section',$defaultAction,sprintf('TEST-COURSE-SECTION-%\'.02d',$i),sprintf('TEST COURSE SECTION %\'.02d',$i),'','','','','','',$offeringCode,"");
                }
            }
            break;
        case "6-Users": //Only case that needs an additional array, $userData, to store data for use in enrollments
            if (version_compare($scenario,2,"<")) {
                if(!isset($userData)) $userData = array(); //If we haven't created the array yet, create it
                for($i=1;$i<=$totalUsers;$i++) {
                    $org_defined_id++;
                    $userToInsert = array('user',$defaultAction,sprintf('test_user_%\'.03d',$i),$org_defined_id,sprintf('TEST USER FIRST %\'.03d',$i),sprintf('TEST USER LAST %\'.03d',$i),'','TRUE','student',sprintf('test%\'.03d@localhost.localdomain',$i),'',sprintf('FIRST %\'.03d',$i),sprintf('LAST %\'.03d',$i),"");
                    $dataForFile[] = $userToInsert;
                    $userData[] = $userToInsert; //Adding to this array for use in enrollments
                }
            }
            break;
        case "7-Enrollments":
            if (version_compare($scenario,1,"==")) {  //Enrolls all users in the first section
                foreach($userData as $value) {
                    $dataForFile[] = array('enrollment',$defaultAction,$value[3],'Student','TEST-COURSE-SECTION-01');
                }
            }
            if (version_compare($scenario,1.1,"<=") && version_compare($scenario,2,"<")) { //Enrolls first user in the first section, then unrolls them x $totalEnrollmentActions
                for($i=1;$i<=$totalEnrollmentActions;$i++) {
                     // Determine the action based on even and odd loop iterations
                    $action = ($i % 2 == 0) ? 'UPDATE' : 'DELETE';
                    $dataForFile[] = array('enrollment',$action,$userData[0][3],'Student','TEST-COURSE-SECTION-01');
                }
            }
            if (version_compare($scenario,1.2,"==")) {  //Enrolls second user in the second section, then unrolls them x 1000 but the last action is an update (enroll)
                for($i=1;$i<=10;$i++) {
                    $dataForFile[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-02');
                    $dataForFile[] = array('enrollment','DELETE',$userData[1][3],'Student','TEST-COURSE-SECTION-02');
                    $dataForFile[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-03');
                    $dataForFile[] = array('enrollment','DELETE',$userData[1][3],'Student','TEST-COURSE-SECTION-03');
                }
                $enrollmentsData[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-04');
            }
            if (version_compare($scenario,1.3,"==")) {  //Enrolls second user in the second section, then unrolls them x 1000 but the last action is an update (enroll) +
                for($i=1;$i<=10;$i++) {
                    $dataForFile[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-02');
                    $dataForFile[] = array('enrollment','DELETE',$userData[1][3],'Student','TEST-COURSE-SECTION-02');
                    $dataForFile[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-03');
                    $dataForFile[] = array('enrollment','DELETE',$userData[1][3],'Student','TEST-COURSE-SECTION-03');
                }
                $dataForFile[] = array('enrollment','UPDATE',$userData[1][3],'Student','TEST-COURSE-SECTION-04');
                //Giving it more to do
                $dataForFile[] = array('enrollment','UPDATE',$userData[2][3],'Student','TEST-COURSE-SECTION-05');
                $dataForFile[] = array('enrollment','UPDATE',$userData[3][3],'Student','TEST-COURSE-SECTION-06');
                $dataForFile[] = array('enrollment','UPDATE',$userData[4][3],'Student','TEST-COURSE-SECTION-07');
                $dataForFile[] = array('enrollment','DELETE',$userData[4][3],'Student','TEST-COURSE-SECTION-07');
            }

            if (version_compare($scenario,1.41,"==")) {  
                $dataForFile[] = array('enrollment','UPDATE',$userData[9][3],'Student','TEST-COURSE-SECTION-07');
            }
            if (version_compare($scenario,1.42,"==")) { 

                $dataForFile[] = array('enrollment','UPDATE',$userData[9][3],'Student','TEST-COURSE-SECTION-05');
                $dataForFile[] = array('enrollment','DELETE',$userData[9][3],'Student','TEST-COURSE-SECTION-07');
            }
            break;
    }

    // Write the data to the CSV file, but only if we have data to write
    if (is_array($dataForFile[0]) && count($dataForFile[0]) > 0) {

        // Generate the file name with the common suffix
        $csvFileName = $fileName . $commonSuffix .'-' .date('YmdHis').".csv";
        $csvFilePaths[] = $csvFileName; // Store the path of the CSV file
        
        // Open the CSV file for writing
        $file = fopen($csvFileName, 'w');

        // Write the header to the CSV file
        fputcsv($file, $header);

        foreach ($dataForFile as $value) {
            
            // Pad the data array if its length is less than the header's length
            if (count($value) > count($header)) die("The data array is longer than the header array.");
            else if (count($value) < count($header)) {
                $value = array_pad($value, count($header), '');
            }

            fputcsv($file, $value);
        }

        // Close the CSV file
        fclose($file);
        unset($dataForFile);
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
