<?php
/*
 * Copyright (c) Derek Smith <DerekSmith@HarmonyFactory.com> 2022
 */


/**
 * Job class
 *
 * All the details representing one job
 * Mirrors the database table structure
 * Mirrors the original spreadsheet structure
 * Active record pattern (mostly)
 *
 * @package    harmonyfactory
 * @subpackage premierlocation
 * @author     Derek Smith <DerekSmith@HarmonyFactory.com>
 */


class class_job
{
    // spreadsheet columns
public string $Metricon_Job_No  ;
public string $FTS_Job_No ;
public string $Lot_No ;
public string $Street_No ;
public string $Street_Name ;
public string $Suburb ;
public string $FTS_Received ;
public string $First_RFI ;
public string $Estate ;
public string $Title_Received ;
public string $PSI_Pack_Received ;
public string $Survey_Received ;
public string $Soil_Report_Received ;
public string $Ordering_Commence_Date ;
public string $Developer_approval_Ordered ;
public string $Developer_approval_Received ;
public string $Warranty_Insurance_Received ;
public string $Structural_Engineering_Ordered ;
public string $Structural_Engineering_Received ;
public string $Energy_report_Received ;
public string $Report_and_Consent_Ordered ;
public string $Report_and_Consent_Received ;
public string $Planning_Consent_Ordered ;
public string $Planning_Consent_Received ;
public string $Levy_Applicable ;
public string $Building_permit_Ordered ;
public string $Building_permit_Received ;
public string $comments ;
public string $PO_1 ;
public string $PO2 ;
public string $BS ;
public string $Division ;


 // new DB columns, not in spreadsheet

public int $id ; //                 int auto_increment primary key,
public int $kind ; //      int  default 0 comment '0=unknown, 1=full assessment, 2=preliminary (eg for OPAL)',
public int $ClientID ; //  int  default 0 comment 'For leter expansion',
public string $Plan_number ; //  varchar(10) DEFAULT '' COMMENT 'Found via obpSearch, Land Vic datashare download',
public string $Spear_ref ; //  varchar(10) DEFAULT '' COMMENT 'Found via Spear screen scrape',
public string $Spear_application_ref ; //  varchar(10) DEFAULT '' COMMENT 'Found via Spear screen scrape',
public string $Spear_milestones ; //  longtext COMMENT 'JSON copy of all milestones data.  Currently, only really interested in the Statement of Compliance (whether issued or not)',
public string $Spear_statement_of_compliance ; //  varchar(10) DEFAULT '' COMMENT 'Either ''Not issued',
public string $EZI_Address ; //  varchar(10) DEFAULT '' COMMENT 'EZI_Address not currently used.  As a concept ...  it might be useful later',
public string $History ; //  longetext COMMENT '' COMMENT 'Machine makes notes of actions.  Of course, a journal table would be better. This is first draft design' ,


//






    /**
     * Creates database table to match spreadsheet
     */

    private function create_database():void
    {
        $create_statement = "
-- columns taken direct from spreadsheet
create table PremierLocation.jobs (
Metricon_Job_No varchar(10) DEFAULT '' COMMENT 'Job number assigned by client eg 717524',
FTS_Job_No varchar(10) DEFAULT '' COMMENT 'FTS-assigned job number eg 21/0089 (year + seq?)',
Lot_No varchar(15) DEFAULT '' COMMENT 'Lot Number. Used to distinguish plot, until street number assigned',
Street number varchar(10) DEFAULT '' COMMENT 'Street Number: Assigned when title granted',
Street_Name varchar(50) DEFAULT '' COMMENT 'Street name - including tyope (eg Avenue/street/way/close etc)',
Suburb varchar(50) DEFAULT '' COMMENT 'Suburb as text',
FTS_Received timestamp DEFAULT 0 COMMENT 'Date FTS received instructions to execute this assessment' , 
1st_RFI timestamp DEFAULT 0 COMMENT 'Date first RFI is sent out' ,
Estate  varchar(50) DEFAULT '' COMMENT 'Text name of the estate eg ''Cloverton'',
Title_Received timestamp DEFAULT 0 COMMENT 'Date Title notification received' ,
PSI_Pack_Received timestamp DEFAULT 0 COMMENT 'Date PSI Pach received' ,
Survey_Received timestamp DEFAULT 0 COMMENT 'Date Survey received' ,
Soil_Report_Received timestamp DEFAULT 0 COMMENT '' ,
Ordering_Commence_Date timestamp DEFAULT 0 COMMENT '' ,
Developer_approval_Ordered timestamp DEFAULT 0 COMMENT '' ,
Developer_approval_Received timestamp DEFAULT 0 COMMENT '' ,
Warranty_Insurance_Received timestamp DEFAULT 0 COMMENT '' ,
Structural_Engineering_Ordered timestamp DEFAULT 0 COMMENT '' ,
Structural_Engineering_Received timestamp DEFAULT 0 COMMENT '' ,
Energy_report_Received timestamp DEFAULT 0 COMMENT '' ,
Report_and_Consent_Ordered timestamp DEFAULT 0 COMMENT '' ,
Report_and_Consent_Received timestamp DEFAULT 0 COMMENT '' ,
Planning_Consent_Ordered timestamp DEFAULT 0 COMMENT '' ,
Planning_Consent_Received timestamp DEFAULT 0 COMMENT '' ,
Levy_Applicable  timestamp DEFAULT 0 COMMENT '' ,
Building_permit_Ordered timestamp DEFAULT 0 COMMENT '' ,
Building_permit_Received timestamp DEFAULT 0 COMMENT '' ,
comments longetext COMMENT 'Any comments entered by user related to this job' , 
PO_1  varchar(10) DEFAULT '' COMMENT 'Either blank, or invoiced',
PO2  varchar(10) DEFAULT '' COMMENT 'Blank, or invoiced',
BuildingSurveyors  varchar(10) DEFAULT '' COMMENT 'Blank, Checkpoint, or text name of other building surveyor',
Division  varchar(10) DEFAULT '' COMMENT 'Division of client (BusinessUnit, effectively. left as text for now',


-- new columns (not part of spreadsheet)
id                int auto_increment primary key,
kind     int  default 0 comment '0=unknown, 1=full assessment, 2=preliminary (eg for OPAL)',
ClientID int  default 0 comment 'For leter expansion',
Plan_number varchar(10) DEFAULT '' COMMENT 'Found via obpSearch, Land Vic datashare download',
Spear_ref varchar(10) DEFAULT '' COMMENT 'Found via Spear screen scrape',
Spear_application_ref varchar(10) DEFAULT '' COMMENT 'Found via Spear screen scrape',
Spear_milestones longtext COMMENT 'JSON copy of all milestones data.  Currently, only really interested in the Statement of Compliance (whether issued or not)',
Spear_statement_of_compliance varchar(10) DEFAULT '' COMMENT 'Either ''Not issued',
EZI_Address varchar(10) DEFAULT '' COMMENT 'EZI_Address not currently used.  As a concept ...  it might be useful later',
History longetext COMMENT '' COMMENT 'Machine makes notes of actions.  Of course, a journal table would be better. This is first draft design' ,

)

        COMMENT 'Fields down to id are direct translations from existing spreadsheet',
        COMMENT 'All this table does is record a job',
        COMMENT 'It might be better for the different options to be a related table - and thus more flexible'
;  -- end table def

create index Metricon_Job_No
    on wp_obp_Document_Template (Metricon_Job_No);

create index FTS_Job_No
    on wp_obp_Document_Template (FTS_Job_No);
    
  create index Spear_ref
    on wp_obp_Document_Template (Spear_ref);
    
    
    
    
";
    }




    /**
     * Returns indexed array of the table column names, matched to spreadsheet column index.
     * @return array
     */
    private function get_column_to_field_map(): array
    {
        $column_to_field_map = [];
        $column_to_field_map[1] = 'Metricon_Job_No';
        $column_to_field_map[2] = 'FTS_Job_No';
        $column_to_field_map[3] = 'Lot_No';
        $column_to_field_map[4] = 'Street_number';
        $column_to_field_map[5] = 'Street_Name';
        $column_to_field_map[6] = 'Suburb';
        $column_to_field_map[7] = 'FTS_Received';
        $column_to_field_map[8] = 'First_RFI';
        $column_to_field_map[9] = 'Estate';
        $column_to_field_map[10] = 'Title_Received';
        $column_to_field_map[11] = 'PSI_Pack_Received';
        $column_to_field_map[12] = 'Survey_Received';
        $column_to_field_map[13] = 'Soil_Report_Received';
        $column_to_field_map[14] = 'Ordering_Commence_Date';
        $column_to_field_map[15] = 'Developer_approval_Ordered';
        $column_to_field_map[16] = 'Developer_approval_Received';
        $column_to_field_map[17] = 'Warranty_Insurance_Received';
        $column_to_field_map[18] = 'Structural_Engineering_Ordered';
        $column_to_field_map[19] = 'Structural_Engineering_Received';
        $column_to_field_map[20] = 'Energy_report_Received';
        $column_to_field_map[21] = 'Report_and_Consent_Ordered';
        $column_to_field_map[22] = 'Report_and_Consent_Received';
        $column_to_field_map[23] = 'Planning_Consent_Ordered';
        $column_to_field_map[24] = 'Planning_Consent_Received';
        $column_to_field_map[25] = 'Levy_Applicable' ;
        $column_to_field_map[26] = 'Building_permit_Ordered';
        $column_to_field_map[27] = 'Building_permit_Received';
        $column_to_field_map[28] = 'comments';
        $column_to_field_map[29] = 'PO_1';
        $column_to_field_map[30] = 'PO2';
        $column_to_field_map[31] = 'BS';
        $column_to_field_map[32] = 'Division';
        return $column_to_field_map ;
    }



}


