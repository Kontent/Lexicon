<?php
/**
 * Lexicon HTML file
 *
 * Based on Lexicon 2.01 by Martin Brampton modified by RolandD of www.csvimproved.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Lexicon
 * @author Granholm Hosting
 * @link http://www.granholmhosting.com
 * @link http://www.csvimproved.com
 * @copyright Copyright (C) 2008 granholmhosting.com
 * @version $Id: admin.lexicon.html.php 178 2008-04-05 13:48:30Z Roland $
 */

defined('_JEXEC') or die('Restricted access');

class HTML_lexicon {

  function showLexiconEntries( $option, &$rows, &$search, &$pageNav, &$clist ) {

    $entrylength   = "40";

    # Table header
    ?>
    <form action="index2.php" method="post" name="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%" class="sectionname">
	    <img src="components/com_lexicon/images/logo.png" valign="top">&nbsp;Lexicon
      </td>
      <td nowrap="nowrap">Display #</td>
			<td>
				<?php echo $pageNav->getLimitBox(); ?>
			</td>
			<td>Search:</td>
			<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
			<td width="right">
				<?php echo $clist;?>
			</td>
    </tr>
    </table>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
      <tr>
        <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
        <th class="title" align=left><div align="left">Term</div></th>
        <th class="title" align=left><div align="left">Category</div></th>
        <th class="title"><div align="center">Published</div></th>
      </tr>
      <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      $row = &$rows[$i];
      echo "<tr class='row$k'>";
      echo "<td width='5%'><input type='checkbox' id='cb$i' name='cid[]' value='$row->id' onclick='isChecked(this.checked);' /></td>";
      echo "<td align='left'><a href=\"index2.php?option=".$option."&task=edit&cid[]=".$row->id."\">$row->tterm</a></td>";
      echo "<td align='left'>$row->category</td>";

      $task = $row->published ? 'unpublish' : 'publish';
      $img = $row->published ? 'publish_g.png' : 'publish_x.png';
      ?>
        <td width="10%" align="center"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>

    </tr>
    <?php    $k = 1 - $k; } ?>
    <tr>
      <th align="center" colspan="7">
        <?php echo $pageNav->getPagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="7">
        <?php echo $pageNav->getPagesCounter(); ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="limitstart" value="<?php echo $pageNav->limitstart; ?>" />
  <input type="hidden" name="boxchecked" value="0" />
  </form>
<?php
}

function editLexicon( $option, &$row, &$publist, &$clist ) {
  require(JPATH_SITE."/administrator/components/com_lexicon/config.lexicon.php");
  $editor =& JFactory::getEditor();
?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      // do field validation
      if (form.tterm.value == ""){
        alert( "Entry must have a term." );
      } else if (form.catid.value == "0"){
	alert( "You must select a category." );
      } else {
      <?php
				echo $editor->save( 'tlexicon' );
		?>
        submitform( pressbutton );
      }
    }
    </script>

    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
    <form action="index2.php" method="post" name="adminForm" id="adminForm">
      <tr>
	    <td width="100%" class="sectionname">
	      <img src="components/com_lexicon/images/logo.png" valign="top">&nbsp;Lexicon
        </td>
	  </tr>
	  <tr>
        <th colspan="2" class="title" >
          <?php echo $row->id ? 'Edit' : 'Add';?> Lexicon Entry
        </th>
      </tr>
      <tr>
        <td valign="top" align="right">Term:</td>
        <td>
          <input class="inputbox" type="text" name="tterm" value="<?php echo $row->tterm; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right">Lexicon:</td>
        <td width="420" valign="top"><?php echo $editor->display( 'tlexicon',  $row->tlexicon, '500', '200', '70', '30' ) ; ?></td>
      </tr>

     <tr>
				<td valign="top" align="right">Category:</td>
				<td>
					<?php echo $clist; ?>
				</td>
			</tr>

      <tr>
        <td width="20%" align="right">Name:</td>
        <td width="80%">
          <input class="inputbox" type="text" name="tname" size="50" maxlength="100" value="<?php echo htmlspecialchars( $row->tname, ENT_QUOTES );?>" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right">E-Mail:</td>
        <td>
          <input class="inputbox" type="text" name="tmail" value="<?php echo $row->tmail; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right">Homepage:</td>
        <td>
          <input class="inputbox" type="text" name="tpage" value="<?php echo $row->tpage; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right">Comment:</td>
        <td>
          <textarea class="inputbox" cols="50" rows="3" name="tcomment" style="width=500px" width="500"><?php echo htmlspecialchars( $row->tcomment, ENT_QUOTES );?></textarea>
        </td>
      </tr>

      <tr>
        <td valign="top" align="right">Published:</td>
        <td>
          <?php echo $publist; ?>
        </td>
      </tr>

    </table>

    <input type="hidden" name="tdate" value="<?php echo date("Y-m-d H:i:s", time()); ?>" />
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="" />
    </form> 
<?php
  }

function multiLexicon( $option, $yesno, $categories, $default_category ) {
  global $mosConfig_absolute_path;
  require(JPATH_SITE."/administrator/components/com_lexicon/config.lexicon.php");
    $editor =& JFactory::getEditor();
echo '
   <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;';
   
  for ($i=1;$i<=$gl_multieditors;$i++)  $editor->save( 'm$i[tlexicon]' ); 
echo '
      if (pressbutton == \'cancel\') {
        submitform( pressbutton );
        return;
      }
 
        submitform( pressbutton );
      
    }
   </script>';
   
   
echo '<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
    <form action="index2.php" method="post" name="adminForm" id="adminForm">
          <tr>
	    <td width="100%" class="sectionname">
	      <img src="components/com_lexicon/images/logo.png" valign="top">&nbsp;Lexicon
        </td>
	  </tr>
	  ';

  
  for ($i=1;$i<=$gl_multieditors;$i++) {
echo '<tr>
        <th colspan="2" class="title" >
           Add Lexicon Entry
        </th>
      </tr><tr>
        <td valign="top" align="right">Term:</td>
        <td>
          <input class="inputbox" type="text" name="m'.$i.'[tterm]" value="" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right">Lexicon:</td>
        <td width="420" valign="top">';
        
/*<textarea name="m'.$i.'[tlexicon]" id="m'.$i.'[tlexicon]" cols="70" rows="30" style="width: 500px; height: 150px;"></textarea>
<br /><script type="text/javascript">
<!-- 
	doclink.addButton(\'m'.$i.'[tlexicon]\');
//-->
</script> */
echo $editor->display( 'm'.$i.'[tlexicon]',  $row->tlexicon, '500', '200', '70', '30' );
//editorArea( 'editor$i',  $row->tlexicon, 'm'.$i.'[tlexicon]', '500', '150', '70', '30' ); 
echo '</td>
     </tr>

     <tr>
			<td valign="top" align="right">Category:</td>
				<td>
					'. JHTMLSelect::genericlist( $categories, 'm'.$i.'[catid]', 'class="inputbox" size="1"', 'value', 'text', $default_category ).' 
				</td>
			</tr>
     <tr>
        <td valign="top" align="right">Published:</td>
        <td>
          '.JHTMLSelect::genericlist( $yesno, 'm'.$i.'[published]', 'class="inputbox" size="2"', 'value', 'text', 1 ).'
        </td>
      </tr>
      <input type="hidden" name="m'.$i.'[id]" value="0" />
      ';
}
echo '</table>
    <input type="hidden" name="id" value="-1" />
    <input type="hidden" name="tdate" value="'.time().'" />
    <input type="hidden" name="option" value="'.$option.'" />
    <input type="hidden" name="task" value="" />
    </form>';
}



# End of class
}
?>
