<?php
/**
 * @version		$Id$
 * @package		Lexicon
 * @subpackage	plgContentLexicon
 * @copyright	Copyright (C) 2008 Rob Schley. All rights reserved.
 * @license		GNU General Public License
 */

defined('JPATH_BASE') or die('Restricted Access');

/**
 * Content Plugin class for Lexicon.
 *
 * @package		Lexicon
 * @subpackage	plgContentLexicon
 * @version		1.0
 */
class plgContentLexicon extends JPlugin
{
	/**
	 * Plugin Constructor.
	 *
	 * @param	object	$subject	The object to observe.
	 * @param	array	$config		An array that holds the plugin configuration.
	 * @since	1.5
	 */
	function plgContentLexicon(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onPrepareContent(&$row, &$params, $page = 0)
	{
		// Get environment data.
		$doc	= &JFactory::getDocument();
		$type	= $doc->getType();
		$print	= JRequest::getBool('print');
		$view	= JRequest::getWord('view');

		// Check if we should add the lexicon tips.
		if (!($type == 'html' && $view == 'article' && !$print && preg_match('#{lexicon="enabled"}#i', $row->text))) {
			// Remove the tag and return.
			$row->text = preg_replace('#{lexicon="enabled"}#i', '', $row->text);
			return true;
		} else {
			// Remove the tag and continue.
			$row->text = preg_replace('#{lexicon="enabled"}#i', '', $row->text);
		}

		// Load the terms for the article.
		$query	= 'SELECT t.id, t.singular, t.plural, t.body'
				. ' FROM #__lexicon_terms AS t'
				. ' JOIN #__lexicon_content_map AS m ON m.term_id = t.id'
				. ' WHERE m.content_id = '.(int)$row->id
				. ' AND t.state = 1';

		$db	= &JFactory::getDBO();
		$db->setQuery($query);
		$terms = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		// If no terms, return true.
		if (count($terms) === 0) {
			return true;
		}

		// Add the lexicon script to the document.
		JHtml::script('lexicon.js', 'plugins/content/lexicon/');



		$fullSkip = array('style', 'script', 'a', 'textarea', 'iframe');
		$deadZone = array();

		/*
		 * Iterate through the terms, search for them in the content,
		 * check their positions, and replace if not in a dead zone.
		 */
		for ($ti = 0, $tc = count($terms); $ti < $tc; $ti++)
		{
			// Identify all of the tags that have content and closing tags to extract them with their content.
			$pattern	= '#(?:\<([\w]+)(?:"[^"]*"|\'[^\']*\'|[^\'">])*(?:\/?)\>)*(?:[^\(</)]*)(?:\</\1>)#mius';
			$matches	= array();
			$count		= preg_match_all($pattern, $row->text, $matches, PREG_OFFSET_CAPTURE);

			/*
			 * Iterate through the tags with closing tags to find content areas
			 * that we may need to skip. Areas like the inside of style, script,
			 * and textareas should not be touched.
			 */
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				// Check if the matched tag name is in the set of tags to skip in full.
				if (in_array($matches[1][$i][0], $fullSkip)) {
					// Add a dead zone to skip the tag and all of it's contents.
					$deadZone[] = array('start' => $matches[0][$i][1], 'end' => ($matches[0][$i][1] + strlen($matches[0][$i][0])));
				}
			}

			// Next, we just find all the tags and add them to our dead zone.
			$pattern	= '#(?:\<[\w]+(?:"[^"]*"|\'[^\']*\'|[^\'">])*(?:\/?)\>)#mius';
			$matches	= array();
			$count		= preg_match_all($pattern, $row->text, $matches, PREG_OFFSET_CAPTURE);

			// Iterate through the tags and add them to the dead zone.
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
				$deadZone[] = array('start' => $matches[0][$i][1], 'end' => ($matches[0][$i][1] + strlen($matches[0][$i][0])));
			}

			// Sort the zones by start and end position.
			usort($deadZone, array('plgContentLexicon', 'zoneSort'));

			$plural		= !empty($terms[$ti]->plural) ? $terms[$ti]->plural.'|' : '';
			$pattern	= '#\b(?:'.$plural.$terms[$ti]->singular.')\b#muis';
			$body		= htmlspecialchars($terms[$ti]->body, ENT_COMPAT, 'UTF-8');
			$matches	= array();

			// Find all instances of the term.
			$count	= preg_match_all($pattern, $row->text, $matches, PREG_OFFSET_CAPTURE);
			$comp	= 0;

			// Did we find any matches for the term?
			if ($count === true) {
				continue;
			}

			/*
			 * A term can be in an article multiple times, iterate through all the
			 * instances that were found and replace them if they are not in a dead zone.
			 */
			for ($j = 0; $j < $count; $j++)
			{
				$term	= $matches[0][$j][0];
				$start	= $matches[0][$j][1];
				$length	= strlen($term);
				$end	= $start + $length;
				$wrap	= false;


				// Check the term's position against the dead zones.
				foreach ($deadZone as $key => $zone)
				{
					// Term is before the first dead zone: break.
					if ($start < $zone['start'] && $end < $zone['start'] && $key == 0) {
						$wrap = true;
						break;
					}
					// Term is within the current dead zone: break.
					elseif ($start > $zone['start'] && $end < $zone['end']) {
						$wrap = false;
						break;
					}
					// Term is past the current dead zone but before the next dead zone: break.
					elseif ($start > $zone['start'] && $end > $zone['end'] && isset($deadZone[$key+1]) && $start < $deadZone[$key+1]['start']) {
						$wrap = true;
						break;
					}
					// Term is past the current dead zone and this is the last one: break.
					elseif ($start > $zone['start'] && $end > $zone['end'] && !isset($deadZone[$key+1])) {
						$wrap = true;
						break;
					}
				}

				// Wrap the term in the appropriate span.
				if ($wrap === true) {
					// We have to account for the length we may have extended already, so add $comp to our start position.
					$replace	= '<span class="lexicon" title="'.$term.'::'.$body.'">'.$term.'</span>';
					$row->text	= substr_replace($row->text, $replace, $start+$comp, $length);
					$comp		+= strlen($replace)-$length;
				}
			}

			$deadZone = array();
		}

		return true;
	}

	/**
	 * Function to handle the sorting comparison for sorting an array of start/end spans for a string.
	 *
	 * @param	array	The first string span to compare.
	 * @param	array	The second string span to compare.
	 * @return	integer	0 for equal, 1 for first greater than second, -1 for second greater than first.
	 */
	function zoneSort($a, $b)
	{
		if (($a['start'] == $b['start']) && ($a['end'] == $b['end'])) {
			return 0;
		} elseif (($a['start'] > $b['start']) || (($a['start'] == $b['start']) && ($a['end'] > $b['end']))) {
			return 1;
		} else {
			return -1;
		}
	}
}