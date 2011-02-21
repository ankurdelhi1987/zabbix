<?php
/*
** Zabbix
** Copyright (C) 2000-2011 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
**/
?>
<?php
require_once(dirname(__FILE__).'/../include/class.cwebtest.php');

class testFormConfigTriggerSeverity extends CWebTest{
	public $affectedTables = array('config');

	// Data provider
	public static function providerTriggerSeverity(){
		// array of data, saveResult, db fields value
		// if saveResult is false. values should not change
		$data = array(
			array(
				array(
					'severity_name_0' => 'sev 0',
					'severity_color_0' => '000000',
					'severity_name_1' => 'sev 1',
					'severity_color_1' => '111111',
					'severity_name_2' => 'sev 2',
					'severity_color_2' => '222222',
					'severity_name_3' => 'sev 3',
					'severity_color_3' => '333333',
					'severity_name_4' => 'sev 4',
					'severity_color_4' => '444444',
					'severity_name_5' => 'sev 5',
					'severity_color_5' => '555555',
				),
				true,
				array(
					'severity_name_0' => 'sev 0',
					'severity_color_0' => '000000',
					'severity_name_1' => 'sev 1',
					'severity_color_1' => '111111',
					'severity_name_2' => 'sev 2',
					'severity_color_2' => '222222',
					'severity_name_3' => 'sev 3',
					'severity_color_3' => '333333',
					'severity_name_4' => 'sev 4',
					'severity_color_4' => '444444',
					'severity_name_5' => 'sev 5',
					'severity_color_5' => '555555',
				)
			),
			array(
				array(
					'severity_name_0' => '',
				),
				false,
				null
			),
			array(
				array(
					'severity_color_0' => '',
				),
				false,
				null
			),
			array(
				array(
					'severity_color_0' => 'ccc',
				),
				false,
				null
			),
			array(
				array(
					'severity_color_0' => 'yuttrt',
				),
				false,
				null
			),
			array(
				array(
					'severity_color_0' => '1234567',
				),
				true,
				array(
					'severity_color_0' => '123456',
				),
			),
			array(
				array(
					'severity_name_0' => 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii more than 32 chars',
				),
				true,
				array(
					'severity_name_0' => 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii',
				),
			),
		);
		return $data;
	}


	public function testFormTriggerSeverity_Layout(){
		$this->login('config.php');
		$this->assertTitle('Configuration of Zabbix');

		$this->dropdown_select_wait('configDropDown', 'Trigger severities');

		$this->ok('Trigger severities');
		$this->ok('CONFIGURATION OF ZABBIX');

		$this->ok(array('Severity 0', 'Severity 1', 'Severity 2', 'Severity 3', 'Severity 4', 'Severity 5'));
		$this->verifyElementPresent('severity_name_0');
		$this->verifyElementPresent('severity_color_0');
		$this->verifyElementPresent('lbl_severity_color_0');
		$this->verifyElementPresent('severity_name_1');
		$this->verifyElementPresent('severity_color_1');
		$this->verifyElementPresent('lbl_severity_color_1');
		$this->verifyElementPresent('severity_name_2');
		$this->verifyElementPresent('severity_color_2');
		$this->verifyElementPresent('lbl_severity_color_2');
		$this->verifyElementPresent('severity_name_3');
		$this->verifyElementPresent('severity_color_3');
		$this->verifyElementPresent('lbl_severity_color_3');
		$this->verifyElementPresent('severity_name_4');
		$this->verifyElementPresent('severity_color_4');
		$this->verifyElementPresent('lbl_severity_color_4');
		$this->verifyElementPresent('severity_name_5');
		$this->verifyElementPresent('severity_color_5');
		$this->verifyElementPresent('lbl_severity_color_5');
		$this->verifyElementPresent('save');

		$this->assertElementPresent('color_picker');
		$this->assertNotVisible('color_picker');
		$this->fireEvent('lbl_severity_color_0', 'click');
		$this->assertVisible('color_picker');
	}

	/**
	 * @dataProvider providerTriggerSeverity
	 */
	public function testFormTriggerSeverity_Update($data, $resultSave, $DBvalues){
		DBsave_tables($this->affectedTables);

		$this->login('config.php');
		$this->dropdown_select_wait('configDropDown', 'Trigger severities');

		foreach($data as $field => $value){
			$this->input_type($field, $value);
		}

		$sql = 'SELECT ' . implode(', ', array_keys($data)) . ' FROM config';
		if(!$resultSave){
			$DBhash = DBhash($sql);
		}

		$this->clickAndWait('save');


		if($resultSave){
			$this->ok('Configuration updated');

			$dbres = DBfetch(DBselect($sql));
			foreach($dbres as $field => $value){
				$this->assertSame($value, $DBvalues[$field], "Value for '$field' was not updated.");
			}
		}
		else{
			$this->ok('ERROR:');
			$this->assertTrue($DBhash === DBhash($sql), "DB fields changed after unsuccessful save.");
		}


		DBrestore_tables($this->affectedTables);
	}

}
?>

