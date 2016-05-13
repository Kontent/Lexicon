<?php

class listConfigHTML extends cmsapiAdminHTML {

	function view ($spec, $config) {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$title = _GLOSSARY_EDIT_CONFIG;
		$icon = "../components/com_glossary/images/glosslogo.png";
		echo <<<CONFIG_HEADING
		
		<form action="index2.php" method="post" name="adminForm">
		<script type="text/javascript" src="js/dhtml.js"></script>
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
   		<tr>
			<td width="75%" colspan="3">
			<div class="title header">
				<img src="$icon" alt="" /> 
				<span class="sectionname">&nbsp;$compname - $title</span>
			</div>
			</td>
			<td width="25%">
			</td>
    	</tr>
    	</table>
		
CONFIG_HEADING;

		$tabs =& new cmsapiPane();
		$tabs->startPane('pane');
		foreach ($spec as $i=>$specdetail) {
			$tabname = $specdetail[0];
			$fields = $specdetail[1];
			$pagenum = $i + 1;
			$tabs->startTab($tabname, 'page'.$pagenum);
			echo <<<TAB_START
			
	<table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminform">
		
TAB_START;
		
			foreach ($fields as $fieldname=>$field) {
				$value = empty($config->$fieldname) ? '' : $config->$fieldname;
				echo $this->viewField ($field[0], $field[1], $fieldname, $field[2], $field[3], $value);
			}

			echo <<<TAB_END
			
	</table>
			
TAB_END;

			$tabs->endTab();
		}
		$tabs->endPane();
		echo <<<END_CONFIG
		
	    <div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="act" value="$this->act" />
			<input type="hidden" name="option" value="com_$cname" />
		</div>
		</form>

END_CONFIG;

	}
	
	function viewField ($legend, $description, $fieldname, $type, $extra, $value) {
		switch ($type) {
			case 'yesno':
				$inputdata = $this->viewYesNo($fieldname, $value);
				break;
			case 'input':
				$inputdata = $this->viewInput($fieldname, $extra, $value);
				break;
			case 'menu':
				$inputdata = $extra;
				break;
			default:
				$inputdata = 'Invalid configuration field specified';
				
		}
		return <<<CONFIG_FIELD
		
		<tr>
			<td>$legend</td>
			<td>$inputdata</td>
			<td>$description</td>
		</tr>
		
CONFIG_FIELD;

	}
	
	function viewYesNo ($fieldname, $value) {
		$selyes = $value ? 'selected="selected"' : '';
		$selno = $value ? '': 'selected="selected"';
		$yes = _CMSAPI_YES;
		$no = _CMSAPI_NO;
		return <<<YES_NO
		
				<select name="$fieldname" class="inputbox">
					<option value="1" $selyes>$yes</option>
					<option value="0" $selno>$no</option>
				</select>
		
YES_NO;

	}
	
	function viewInput ($fieldname, $size, $value) {
		
		return <<<SIMPLE_INPUT
		
				<input type="text" class="inputbox" name="$fieldname" size="$size" value="$value" />
				
SIMPLE_INPUT;
		
	}
	

}