<?php
require '../../../vendor/autoload.php';

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./tmpl_ass_move_form.xls');

$worksheet = $spreadsheet->getActiveSheet();

$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
// Increment the highest column letter
$highestColumn++;

for ($row = 1; $row <= $highestRow; ++$row) {
    for ($col = 'A'; $col != $highestColumn; ++$col) {

        switch ($worksheet->getCell($col . $row) -> getValue()) {

            case "TRANSMITTER": // giver
                $worksheet->getCell($col . $row) -> setValue('Сергей Сергеевич Иванов');
                break;
            case "RECEIVER": // giver
                $worksheet->getCell($col . $row) -> setValue('Иван Михайлович Сергеев');
                break;
            case "MOVEDATE": // giver
                $worksheet->getCell($col . $row) -> setValue('13/10/2019');
                break;
            case "FIRSTASSET": // giver
                $worksheet->getCell($col . $row) -> setValue('Dell Computer');
                break;
            case "ASSETTAG": // giver
                $worksheet->getCell($col . $row) -> setValue('12203210/2');
                break;
            case "QUANTITY": // giver
                $worksheet->getCell($col . $row) -> setValue('1');
                break;
            case "TRANSMITTER4SIGN": // giver
                $worksheet->getCell($col . $row) -> setValue('Иванов C.C.');
                break;
            case "RECEIVRE4SIGN": // giver
                $worksheet->getCell($col . $row) -> setValue('Сергеев И. М.');
                break;
        }
    }

}




//$worksheet->getCell('K8')->setValue('John');
//$worksheet->getCell('N10')->setValue('Smith');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
$writer->save('./ass_move_form.xls');



#=============
/*
date_default_timezone_set('Europe/Moscow');

$connection = mysqli_connect('localhost', 'user', 'ZijPwa5l', 'sales_stat') ;
if(mysqli_connect_errno()){
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

mysqli_set_charset($connection,"utf8");

$sql_clients = 'INSERT INTO `clients` ' .
    "(`number`, `name`, `in_sap`, `location`) " .
    ' VALUES ' ;
$sql_docs = 'INSERT INTO `docs` ' .
    "(`id`, `number`, `date`, `position_num`, `material_num`) " .
    ' VALUES ' ;
$sql_salesman = 'INSERT INTO `salesman` ' .
    "( `sap_number`, `w_name`, `f_name`, `m_name`, `l_name`, `location`) " .
    ' VALUES ' ;
$sql_material = 'INSERT INTO `material` ' .
    "( `number`, `name`) " .
    ' VALUES ' ;
$sql_sales =  'INSERT INTO `sales` ' .
    "(`salesman_n`, `doc_n`, `doc_sum`, `client_id`) " .
    ' VALUES ' ;

$cr_table_name = "update_table_kz";

$sql_update_table_kz = 'INSERT INTO `' . $cr_table_name . '` ' .
    "( `sl_name`, `f_name`, `m_name` , `l_name`, " .
    "`doc_num`, `doc_date`, " .
    "`cl_code`, `cl_num`, `cl_name`, " .
    "`mat_num`, `mat_name`, ".
    "`pos_quant`, `pos_sum`, `cost`," .
    "`location` ) ".
    ' VALUES ' ;

if ( $xlsx = SimpleXLSX::parse('Январь корректировочная.xlsx') ) {



    $rows = $xlsx->rows();
    $row_c = count($rows);
//print_r($xlsx);

    $cl_code = 0;
    $location = "398"; //KZ




    for ($i = 0 ; $i < $row_c; $i++) { // header cut
        if ( $xlsx->worksheet()->sheetData->row[$i]["outlineLevel"] != "") {
            $i--;
            break;
        }
    }

    

    $sql_drop_table ="DROP TABLE IF EXISTS " . $cr_table_name;

    $result = mysqli_query($connection, $sql_drop_table);
    if ( $result === FALSE ) {
        die(mysqli_error($connection));
    }

    $sql_cr_update_table_kz = 'CREATE TABLE `' . $cr_table_name .'` (' .
        '`sl_name` text NOT NULL, `f_name` text NOT NULL, `m_name` text NOT NULL, `l_name` text NOT NULL, ' .
        '`doc_num` int(11) NOT NULL, `doc_date` datetime NOT NULL, ' .
        '`cl_code` int(11) NOT NULL, `cl_num` int(11) NOT NULL, `cl_name` text NOT NULL, ' .
        '`mat_num` text NOT NULL, `mat_name` text NOT NULL, '.
        '`pos_quant` int(11) NOT NULL, `pos_sum` decimal(15,2) NOT NULL, `cost` decimal(15,2) NOT NULL,'.
        '`location` int(11) NOT NULL )';

    $result = mysqli_query($connection, $sql_cr_update_table_kz);
    if ( $result === FALSE ) {
        die(mysqli_error($connection));
    }

    $sql_update_table_kz = rtrim($sql_update_table_kz, ', ');
    //   print ($sql_update_table_kz);

    $result = mysqli_query($connection, $sql_update_table_kz);
    if ( $result === FALSE ) {
        die(mysqli_error($connection));
    }



  //      $sql_material = rtrim($sql_material, ', ');
  //      print $sql_material;
//
  //      $result = mysqli_query($connection, $sql_material);
  //      if ( $result === FALSE ) {Reader
  //          die(mysqli_error($connection));
  //      }



$connection->close();

*/


