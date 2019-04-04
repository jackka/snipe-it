<?php

namespace PhpOffice\PhpSpreadsheet;
use phpoffice\phpspreadsheet;
use DB;

class AssetMoveForm extends Spreadsheet

{
    public static function FillForm($data)
    {
        $working_dir = app_path().'/Notifications/';
        $spreadsheet = IOFactory::load($working_dir . 'AssetMoveForm_tmpl.xls');

        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
// Increment the highest column letter
        $highestColumn++;
        
        $lognumber = 0;    
        
        for ($row = 1;
             $row <= $highestRow;
             ++$row) {
            for ($col = 'A';
                 $col != $highestColumn;
                 ++$col) {

                switch ($worksheet->getCell($col . $row)->getValue()) {

                    case "TRANSMITTER":
                        $value = $data->admin["attributes"]["notes"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "RECEIVER":
                        $value = $data->target["attributes"]["notes"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "MOVEDATE":
                        $value = $data->last_checkout;
                        $val_arr = explode( '-', $value );
                        $worksheet->getCell($col . $row)->setValue($val_arr[2] . "/" . $val_arr[1] . "/" . $val_arr[0] );
                        break;
                    case "FIRSTASSET":
                        $value = $data->item["attributes"]["name"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "ASSETTAG":
                        $value = $data->item["attributes"]["asset_tag"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "QUANTITY":
                        $worksheet->getCell($col . $row)->setValue('1');
                        break;
                    case "TRANSMITTER4SIGN":
                        $value = $data->admin["attributes"]["notes"];
                        $val_arr = explode( ' ', $value );
                        if (count($val_arr) == 3 ) {
                            $val_arr[1] = mb_substr($val_arr[1], 0, 1, "utf-8");
                            $val_arr[2] = mb_substr($val_arr[2], 0, 1, "utf-8");
                            $worksheet->getCell($col . $row)->setValue($val_arr[0] . " " . $val_arr[1] . ". " . $val_arr[2] . ".");
                            } else {
                            $worksheet->getCell($col . $row)->setValue("ОШИБКА");
                        }
                        break;
                    case "RECEIVER4SIGN":
                        $value = $data->target["attributes"]["notes"];
                        $val_arr = explode( ' ', $value );
                        if (count($val_arr) == 3 ) {
                            $val_arr[1] = mb_substr($val_arr[1], 0, 1, "utf-8");
                            $val_arr[2] = mb_substr($val_arr[2], 0, 1, "utf-8");
                            $worksheet->getCell($col . $row)->setValue($val_arr[0] . " " . $val_arr[1] . ". " . $val_arr[2] . ".");
                            } else {
                            $worksheet->getCell($col . $row)->setValue("ОШИБКА");
                        }
                        break;
                    case "LOGNUMBER":
                        $value = $data->log_id;
                        $lognumber = $value;
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "COST":
                        $value = $data->item["attributes"]["purchase_cost"];
                        if ($value == "" ){
                            $value = "менее 40 тыс. руб.";
                        }
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "MOVEDATE_D":
                        $value = $data->last_checkout;
                        $val_arr = explode( '-', $value );
                        $worksheet->getCell($col . $row)->setValue($val_arr[2]);
                        break;
                    case "MOVEDATE_M":
                        $value = $data->last_checkout;
                        $val_arr = explode( '-', $value );
                        $worksheet->getCell($col . $row)->setValue($val_arr[1]);
                        break;
                    case "MOVEDATE_Y":
                        $value = $data->last_checkout;
                        $val_arr = explode( '-', $value );
                        $val_arr[0] = substr($val_arr[0], 2, 2);
                        $worksheet->getCell($col . $row)->setValue($val_arr[0]);
                        break;
                    case "TRANSMITTER_POS":
                        $value = $data->admin["attributes"]["jobtitle"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "RECEIVER_POS":
                        $value = $data->target["attributes"]["jobtitle"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "TRANSMITTER_DEP":
                        $value = $data->admin["attributes"]["department_id"];
                        if (isset($value)){
                            $value = DB::table('departments')->where('id','=', $value )->get()->first()->{'name'};
                        } else {
                            $value = "ОШИБКА";
                        }
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "RECEIVER_DEP":
                        $value = $data->target["attributes"]["department_id"];
                        if (isset($value)){
                            $value = DB::table('departments')->where('id','=', $value )->get()->first()->{'name'};
                        } else {
                            $value = "ОШИБКА";
                        }
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "PURCHASE_DATE":
                        $value = $data->item["attributes"]["purchase_date"];
                        $val_arr = explode( '-', $value );
                        $worksheet->getCell($col . $row)->setValue($val_arr[2] . "/" . $val_arr[1] . "/" . $val_arr[0] );
                        break;
                    case "RECEIVER_NUMBER":
                        $value = $data->target["attributes"]["employee_num"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;
                    case "TRANSMITTER_NUMBER":
                        $value = $data->admin["attributes"]["employee_num"];
                        $worksheet->getCell($col . $row)->setValue($value);
                        break;

                }
            }
        }


//$worksheet->getCell('K8')->setValue('John');
//$worksheet->getCell('N10')->setValue('Smith');

        $writer = IOFactory::createWriter($spreadsheet, "Xls");
        $writer->save($working_dir . $lognumber. '_AssetMoveForm.xls');


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
          //          die(mysqli_error($connection));RECEIVRE4SIGN
          //      }
        
        
        
        $connection->close();
        
        */

    }
}