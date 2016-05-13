<?php
/**
 * @version		$Id$
 * @package		Lexicon
 * @subpackage	com_lexicon
 * @copyright	Copyright (C) 2008 Rob Schley. All rights reserved.
 * @license		GNU General Public License
 */

defined('JPATH_BASE') or die('Restricted Access');

/**
 * Finder adapter for Lexicon.
 *
 * @package		Lexicon
 * @subpackage	plgFinderLexicon
 * @version		1.0
 */
class plgFinderLexicon extends JPlugin
{
	function onInitIndex()
	{
		// Get the indexer state.
		$state	= JXIndexerHelper::getState();
		$db		= &JFactory::getDBO();
		$ctotal	= 0;

		// Check that lexicon is installed.
		$db->setQuery('SHOW TABLES LIKE '.$db->Quote($db->replacePrefix('#__lexicon_terms')));

		// If the table is present, get the number of items.
		if ($db->loadResult())
		{
			// Get the total number of terms.
			$db->setQuery('SELECT count(`id`) FROM `#__lexicon_terms`');
			$ttotal = $db->loadResult();

			// Check for a database error.
			if ($db->getErrorNum()) {
				$this->_subject->setError($db->getErrorMsg());
				return false;
			}

			// If there are no terms, we don't need to deal with the content items.
			if ($ttotal)
			{
				// Get the total number of content items.
				$db->setQuery('SELECT count(`id`) FROM `#__content`');
				$ctotal = $db->loadResult();

				// Check for a database error.
				if ($db->getErrorNum()) {
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}

				// Load the published singular and plural terms.
				$db->setQuery('SELECT `id`, `singular` AS term FROM `#__lexicon_terms` WHERE LENGTH(`singular`) > 1'
							. ' UNION SELECT `id`, `plural` AS term FROM `#__lexicon_terms` WHERE LENGTH(`plural`) > 1');
				$terms = $db->loadObjectList();

				// Check for a database error.
				if ($db->getErrorNum()) {
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}

				// Convert all the terms to lowercase.
				if (count($terms)) {
					for ($i = 0, $c = count($terms); $i < $c; $i++) {
						$terms[$i]->term = JString::strtolower($terms[$i]->term);
					}
				}

				// Set the state for the lexicon terms.
				$state->pluginState['Lexicon']['count'] = (int)$ttotal;
				$state->pluginState['Lexicon']['terms'] = $terms;
			}

			// Add the total number of items to index to the indexer state
			$state->totalItems += (int)$ctotal;
		}

		// Populate the indexer state plugin data
		$state->pluginState['Lexicon']['total']	= (int)$ctotal;
		$state->pluginState['Lexicon']['offset'] = 0;

		// Set the indexer state
		JXIndexerHelper::setState($state);
	}

	function onBuildIndex()
	{
		// Get the indexer state.
		$state = JXIndexerHelper::getState();

		// If the batch is over or we have no more items to index, return true.
		if ((($state->batchSize !== false) and ($state->batchSize < 1)) or ($state->pluginState['Lexicon']['offset'] == $state->pluginState['Lexicon']['total'])) {
			return true;
		}

		// Initialize variables.
		$db		= &JFactory::getDBO();
		$offset	= $state->pluginState['Lexicon']['offset'];
		$terms	= $state->pluginState['Lexicon']['terms'];
		$limit	= $state->batchSize;

		// Get the items to index.
		$db->setQuery('SELECT `id`, CONCAT(`introtext`, `fulltext`) AS text FROM `#__content`', $offset, $limit);
		$items = $db->loadObjectList();

		// Check for a database error
		if ($db->getErrorNum()) {
			$this->_subject->setError($db->getErrorMsg());
			return false;
		}

		// Iterate through the items.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Perform any necessary processing.
			$id		= (int)$items[$i]->id;
			$text	= JXIndexerHelper::prepareText($items[$i]->text);
			$maps	= array();

			/*
			 * Parsing the string input into keywords is a multi-step process.
			 *
			 * Regexes:
			 *	1. Removes parantheses around numbers.
			 *	2. Removes everything accept word characters, plus, minus, period, and comma.
			 *	3. Removes plus, minus, period, and comma from around word characters.
			 *	4. Removes multiple space chracters and replaces with a single space.
			 */
			if (JX_FINDER_UNICODE) {
				$text	= JString::strtolower($text);
				$text	= preg_replace('#\(([\pN]+)\)*#mui', '$1', $text);
				$text	= preg_replace('#[^\pL\pM\pN+-.,\']+#mui', ' ', $text);
				$text	= preg_replace('#[+-.,]*([\pL\pM-\']+)[+-.,]*#mui', ' $1 ', $text);
				$text	= preg_replace('#\s+#mui', ' ', $text);
			} else {
				$text	= JString::strtolower($text);
				$text	= preg_replace('#\(([\d]+)\)*#mi', '$1', $text);
				$text	= preg_replace('#[^\w\d+-.,\']+#mi', ' ', $text);
				$text	= preg_replace('#[+-.,]*([\w-\']+)[+-.,]*#mi', ' $1 ', $text);
				$text	= preg_replace('#\s+#mi', ' ', $text);
			}

			// Search for the terms in the text.
			for ($j = 0, $k = count($terms); $j < $k; $j++)
			{
				// Check if the term is in the text.
				if (JString::strpos($text, $terms[$j]->term) !== false) {
					$maps[] = (int)$terms[$j]->id;
				}
			}

			// Remove duplicate maps and sanitize.
			$maps = array_unique($maps);

			if (count($maps))
			{
				// Delete the current set of maps for the content item.
				$db->setQuery('DELETE FROM `#__lexicon_content_map` WHERE `content_id` = '.$id);
				$db->query();

				// Check for a database error
				if ($db->getErrorNum()) {
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}

				// Insert the new maps for the content item.
				$values = count($maps) == 1 ? $id.', '.$maps[0] : $id.', '.implode(' ), ( '.$id.', ', $maps);
				$db->setQuery('INSERT INTO `#__lexicon_content_map` (`content_id`, `term_id`) VALUES ('.$values.' )');
				$db->query();

				// Check for a database error
				if ($db->getErrorNum()) {
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}
			}

			// Increment the offset.
			$offset++;
			$state->batchSize--;
			$state->totalItems--;
		}

		// set the indexer state
		$state->pluginState['Lexicon']['offset'] = $offset;
		JXIndexerHelper::setState($state);

		return true;
	}
}