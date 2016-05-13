<?php

class editEntriesHTML extends cmsapiAdminHTML {
	
	function edit ($entry, $glossaries) {
		$glosslist = $this->makeGlossarySelect($glossaries);
		
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$title = _GLOSSARY_EDIT_ENTRIES;
		$icon = "../components/com_glossary/images/glosslogo.png";

		$gl_select = _GLOSSARY_GLOSSARY_SELECT;
		$gl_letter = _GLOSSARY_LETTER;
		$gl_term = _GLOSSARY_TERM;
		$gl_definition = _GLOSSARY_DEFINITION;
		$gl_name = _GLOSSARY_AUTHOR_NAME;
		$gl_locality = _GLOSSARY_LOCALITY;
		$gl_mail = _GLOSSARY_MAIL;
		$gl_page = _GLOSSARY_PAGE;
		$gl_date = _GLOSSARY_DATE;
		$gl_comment = _GLOSSARY_COMMENT;
		$gl_published = _GLOSSARY_PUBLISHED;
		
		$yes = _CMSAPI_YES;
		$no = _CMSAPI_NO;

		$yescheck = $entry->published ? 'checked="checked"' : '';
		$nocheck = $entry->published ? '' : 'checked="checked"';

		echo <<<EDIT_ENTRY

	<div id="glossary">
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
    		<label for="catid">$gl_select:</label>
    		$glosslist
    	</div>
    	<div>
    		<label for="tletter">$gl_letter:</label>
    		<input name="tletter" id="tletter" type="text" class="inputbox" value="$entry->tletter" />
    	</div>
    	<div>
    		<label for="tterm">$gl_term:</label>
    		<input name="tterm" id="tterm" type="text" class="widthspec inputbox" size="100" value="$entry->tterm" />
    	</div>
    	<div>
    		<label for="tdefinition">$gl_definition:</label>
    		<textarea name="tdefinition" id="tdefinition" class="widthspec inputbox" rows="6" cols="40">$entry->tdefinition</textarea>
    	</div>
    	<div>
    		<label for="tname">$gl_name:</label>
    		<input name="tname" id="tname" type="text" class="widthspec inputbox" size="100" value="$entry->tname" />
    	</div>
    	<div>
    		<label for="tloca">$gl_locality:</label>
    		<input name="tloca" id="tloca" type="text" class="widthspec inputbox" size="100" value="$entry->tloca" />
    	</div>
    	<div>
    		<label for="tmail">$gl_mail:</label>
    		<input name="tmail" id="tmail" type="text" class="widthspec inputbox" size="100" value="$entry->tmail" />
    	</div>
    	<div>
    		<label for="tpage">$gl_page:</label>
    		<input name="tpage" id="tpage" type="text" class="widthspec inputbox" size="100" value="$entry->tpage" />
    	</div>
    	<div>
    		<label for="tdate">$gl_date:</label>
    		<input name="tdate" id="tdate" type="text" class="widthspec inputbox" size="100" value="$entry->tdate" />
    	</div>
    	<div>
    		<label for="published">$gl_published:</label>
    		<input name="published" id="" type="radio" class="inputbox" value="1" $yescheck /><span class="glossary">$yes</span>
    		<input name="published" type="radio" class="inputbox" value="0" $nocheck /><span class="glossary">$no</span>
    	</div>
	    <div class="clear">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="act" value="$this->act" />
			<input type="hidden" name="option" value="com_$cname" />
			<input type="hidden" name="glossid" value="$entry->catid" />
			<input type="hidden" name="id" value="$entry->id" />
		</div>
    	</form>
    </div>
		
EDIT_ENTRY;

	}
	
	function makeGlossarySelect ($glossaries) {
		$optionlist = '';
		foreach ($glossaries as $glossary) $optionlist .= <<<OPTION_ENTRY
		
			<option value="$glossary->id">$glossary->name</option>
			
OPTION_ENTRY;

		return <<<OPTION_LIST
		
		<select name="catid" id="catid" class="glossary inputbox">
			$optionlist
		</select>
	
OPTION_LIST;
		
	}
	
}