<?php

class editGlossariesHTML extends cmsapiAdminHTML {
	
	function edit ($glossary) {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$title = _GLOSSARY_EDIT_ENTRIES;
		$icon = "../components/com_glossary/images/glosslogo.png";

		$gl_name = _GLOSSARY_NAME;
		$gl_description = _GLOSSARY_DESCRIPTION;
		$gl_published = _GLOSSARY_PUBLISHED;
		
		$yes = _CMSAPI_YES;
		$no = _CMSAPI_NO;

		$yescheck = $glossary->published ? 'checked="checked"' : '';
		$nocheck = $glossary->published ? '' : 'checked="checked"';

		echo <<<EDIT_GLOSSARY
		
		<form action="index2.php" method="post" name="adminForm">
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
    	<div>
    		<label for="name">$gl_name:</label>
    		<input name="name" id="name" type="text" class="inputbox" value="$glossary->name" />
    	</div>
    	<div>
    		<label for="description">$gl_description:</label>
    		<input name="description" id="description" type="text" class="inputbox" size="100" value="$glossary->description" />
    	</div>
    	<div>
    		<label for="published">$gl_published:</label>
    		<input name="published" id="published" type="radio" class="inputbox" value="1" $yescheck /><span class="glossary">$yes</span>
    		<input name="published" type="radio" class="inputbox" value="0" $nocheck /><span class="glossary">$no</span>
    	</div>
	    <div class="clear">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="act" value="$this->act" />
			<input type="hidden" name="option" value="com_$cname" />
			<input type="hidden" name="id" value="$glossary->id" />
		</div>
    	</form>
		
EDIT_GLOSSARY;

	}
}