<?php

class glossaryEditHTML {
	
	function edit ($entry, $my, $gconfig) {
		$compname = _THIS_COMPONENT_NAME;
		$cname = strtolower($compname);
		$title = _GLOSSARY_EDIT_ENTRIES;
		$icon = "../components/com_glossary/images/glosslogo.png";

		$gl_letter = _GLOSSARY_LETTER;
		$gl_term = _GLOSSARY_TERM;
		$gl_definition = _GLOSSARY_DEFINITION;
		$gl_name = _GLOSSARY_USER_NAME;
		$gl_locality = _GLOSSARY_USER_LOCALITY;
		$gl_mail = _GLOSSARY_USER_MAIL;
		$gl_uri = _GLOSSARY_USER_URI;
		
		$gl_submit = _GLOSSARY_USER_SUBMIT;
		$gl_clear = _GLOSSARY_USER_CLEAR;
		
		if ($my->id) {
			$entry->tname = $my->name;
			$entry->tmail = $my->email;
		}
	
		if ($gconfig->anonentry) echo <<<EDIT_ENTRY_FREE
		
		<form action="index2.php" method="post" name="adminForm">
		<h2>$compname $title</h2>
    	<div>
    		<label for="tname">$gl_name:</label>
    		<input name="tname" id="tname" type="text" class="inputbox" size="50" value="$entry->tname" />
    	</div>
    	<div>
    		<label for="tmail">$gl_mail:</label>
    		<input name="tmail" id="tmail" type="text" class="inputbox" size="50" value="$entry->tmail" />
    	</div>
    	<div>
    		<label for="tpage">$gl_uri:</label>
    		<input name="tpage" id="tpage" type="text" class="inputbox" size="50" value="$entry->tpage" />
    	</div>
    	<div>
    		<label for="tloca">$gl_locality:</label>
    		<input name="tloca" id="tloca" type="text" class="inputbox" size="50" value="$entry->tloca" />
    	</div>
    	<div>
    		<label for="tletter">$gl_letter:</label>
    		<input name="tletter" id="tletter" type="text" class="inputbox" value="$entry->tletter" />
    	</div>
    	<div>
    		<label for="tterm">$gl_term:</label>
    		<input name="tterm" id="tterm" type="text" class="inputbox" size="50" value="$entry->tterm" />
    	</div>
    	<div>
    		<label for="tdefinition">$gl_definition:</label>
    		<textarea id="tdefinition" name="tdefinition" class="inputbox" rows="4" cols="30">$entry->tdefinition</textarea>
    	</div>
    	<div>
    		<input type="submit" class="button clear" value="$gl_submit" />
    		<input type="reset" class="button" value="$gl_clear" />
    	</div>
	    <div class="clear">
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="option" value="com_$cname" />
			<input type="hidden" name="catid" value="$entry->catid" />
			<input type="hidden" name="id" value="$entry->id" />
		</div>
    	</form>
		
EDIT_ENTRY_FREE;

		else echo <<<EDIT_ENTRY_LIMITED
		
		<form action="index2.php" method="post" name="adminForm">
		<h2>$compname $title</h2>
    	<div>
    		<label for="tname">$gl_name:</label>
    		<input name="tname" id="tname" type="text" class="inputbox" readonly="readonly" size="50" value="$entry->tname" />
    	</div>
    	<div>
    		<label for="tmail">$gl_mail:</label>
    		<input name="tmail" id="tmail" type="text" class="inputbox" readonly="readonly" size="50" value="$entry->tmail" />
    	</div>
    	<div>
    		<label for="tpage">$gl_uri:</label>
    		<input name="tpage" id="tpage" type="text" class="inputbox" size="50" value="$entry->tpage" />
    	</div>
    	<div>
    		<label for="tloca">$gl_locality:</label>
    		<input name="tloca" id="tloca" type="text" class="inputbox" size="50" value="$entry->tloca" />
    	</div>
    	<div>
    		<label for="tletter">$gl_letter:</label>
    		<input name="tletter" id="tletter" type="text" class="inputbox" value="$entry->tletter" />
    	</div>
    	<div>
    		<label for="tterm">$gl_term:</label>
    		<input name="tterm" id="tterm" type="text" class="inputbox" size="50" value="$entry->tterm" />
    	</div>
    	<div>
    		<label for="tdefinition">$gl_definition:</label>
    		<textarea id="tdefinition" name="tdefinition" class="inputbox" rows="4" cols="30">$entry->tdefinition</textarea>
    	</div>
    	<div>
    		<input type="submit" class="button clear" value="$gl_submit" />
    		<input type="reset" class="button" value="$gl_clear" />
    	</div>
	    <div class="clear">
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="option" value="com_$cname" />
			<input type="hidden" name="catid" value="$entry->catid" />
			<input type="hidden" name="id" value="$entry->id" />
		</div>
    	</form>
		
EDIT_ENTRY_LIMITED;

	}

	function makeGlossarySelect ($glossaries) {
		$optionlist = '';
		foreach ($glossaries as $glossary) $optionlist .= <<<OPTION_ENTRY
		
			<option value="$glossary->id">$glossary->name</option>
			
OPTION_ENTRY;

		return <<<OPTION_LIST
		
		<select name="catid" class="inputbox">
			$optionlist
		</select>
	
OPTION_LIST;
		
	}

}