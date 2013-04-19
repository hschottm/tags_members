<?php

/**
 * @copyright  Helmut Schottmüller 2008-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/tags_members>
 * @package    tags_members
 * @license    LGPL 
 * @filesource
 */

namespace Contao;

/**
 * Class ModuleMemberListTags
 *
 * @copyright  Helmut Schottmüller 2009
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    memberextensions
 */
class ModuleMemberListTags extends ModuleMemberlist
{
	/**
	 * List all members
	 */
	protected function listAllMembers()
	{
		if (strlen($this->Input->get('tag')))
		{
			$relatedlist = (strlen($this->Input->get('related'))) ? preg_split("/,/", $this->Input->get('related')) : array();
			$alltags = array_merge(array($this->Input->get('tag')), $relatedlist);
			$tagids = array();
			$first = true;
			foreach ($alltags as $tag)
			{
				if (strlen(trim($tag)))
				{
					if (count($tagids))
					{
						$tagids = $this->Database->prepare("SELECT id FROM tl_tag WHERE from_table = ? AND tag = ? AND  id IN (" . join($tagids, ",") . ")")
							->execute('tl_member', $tag)
							->fetchEach('id');
					}
					else if ($first)
					{
						$tagids = $this->Database->prepare("SELECT id FROM tl_tag WHERE from_table = ? AND tag = ?")
							->execute('tl_member', $tag)
							->fetchEach('id');
						$first = false;
					}
				}
			}
			$arrValidMembers = $tagids;
			if (count($arrValidMembers) == 0) 
			{
				$this->Template->thead = array();
				$this->Template->tbody = array();

				// Pagination
				$objPagination = new Pagination($objTotal->count, $per_page);
				$this->Template->pagination = $objPagination->generate("\n  ");
				$this->Template->per_page = $per_page;

				// Template variables
				$this->Template->action = ampersand($this->Environment->request);
				$this->Template->search_label = specialchars($GLOBALS['TL_LANG']['MSC']['search']);
				$this->Template->per_page_label = specialchars($GLOBALS['TL_LANG']['MSC']['list_perPage']);
				$this->Template->search = $this->Input->get('search');
				$this->Template->for = $this->Input->get('for');
				$this->Template->order_by = $this->Input->get('order_by');
				$this->Template->sort = $this->Input->get('sort');
				return;
			}
			$time = time();
			$arrFields = $this->arrMlFields;
			$intGroupLimit = (count($this->arrMlGroups) - 1);
			$arrValues = array();
			$strWhere = '';

			// Search query
			if ($this->Input->get('search') && $this->Input->get('for') != '' && $this->Input->get('for') != '*')
			{
				$strWhere .= $this->Input->get('search') . " REGEXP ? AND ";
				$arrValues[] = $this->Input->get('for');
			}

			$strOptions = '';
			$arrSortedFields = array();

			// Sort fields
			foreach ($arrFields as $field)
			{
				$arrSortedFields[$field] = $GLOBALS['TL_DCA']['tl_member']['fields'][$field]['label'][0];
			}

			natcasesort($arrSortedFields);

			// Add searchable fields to drop-down menu
			foreach ($arrSortedFields as $k=>$v)
			{
				$strOptions .= '  <option value="' . $k . '"' . (($k == $this->Input->get('search')) ? ' selected="selected"' : '') . '>' . $v . '</option>' . "\n";
			}

			$this->Template->search_fields = $strOptions;
			$strWhere .= "(";

			// Filter groups
			for ($i=0; $i<=$intGroupLimit; $i++)
			{
				if ($i < $intGroupLimit)
				{
					$strWhere .= "groups LIKE ? OR ";
					$arrValues[] = '%"' . $this->arrMlGroups[$i] . '"%';
				}
				else
				{
					$strWhere .= "groups LIKE ?) AND ";
					$arrValues[] = '%"' . $this->arrMlGroups[$i] . '"%';
				}
			}

			// List active members only
			if (in_array('username', $arrFields))
			{
				$strWhere .= "(publicFields!='' OR allowEmail=? OR allowEmail=?) AND disable!=1 AND (start='' OR start<=?) AND (stop='' OR stop>=?)";
				array_push($arrValues, 'email_member', 'email_all', $time, $time);
			}
			else
			{
				$strWhere .= "publicFields!='' AND disable!=1 AND (start='' OR start<=?) AND (stop='' OR stop>=?)";
				array_push($arrValues, $time, $time);
			}

			// Get total number of members
			$objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_member WHERE id IN (" . join(',', $arrValidMembers) . ") AND " . $strWhere)
							 ->execute($arrValues);

			// Split results
			$page = $this->Input->get('page') ? $this->Input->get('page') : 1;
			$per_page = $this->Input->get('per_page') ? $this->Input->get('per_page') : $this->perPage;
			$order_by = $this->Input->get('order_by') ? $this->Input->get('order_by') . ' ' . $this->Input->get('sort') : 'username';

			// Begin query
			$objMemberStmt = $this->Database->prepare("SELECT id, username, publicFields, " . implode(', ', $this->arrMlFields) . " FROM tl_member WHERE id IN (" . join(',', $arrValidMembers) . ") AND " . $strWhere . " ORDER BY " . $order_by);

			// Limit
			if ($per_page)
			{
				$objMemberStmt->limit($per_page, (($page - 1) * $per_page));
			}

			$objMember = $objMemberStmt->execute($arrValues);

			// Prepare URL
			$strUrl = preg_replace('/\?.*$/', '', $this->Environment->request);
			$this->Template->url = $strUrl;
			$blnQuery = false;

			// Add GET parameters
			foreach (preg_split('/&(amp;)?/', $_SERVER['QUERY_STRING']) as $fragment)
			{
				if (strlen($fragment) && strncasecmp($fragment, 'order_by', 8) !== 0 && strncasecmp($fragment, 'sort', 4) !== 0 && strncasecmp($fragment, 'page', 4) !== 0)
				{
					$strUrl .= (!$blnQuery ? '?' : '&amp;') . $fragment;
					$blnQuery = true;
				}
			}

			$strVarConnector = $blnQuery ? '&amp;' : '?';

			// Prepare table
			$arrTh = array();
			$arrTd = array();

			// THEAD
			for ($i=0; $i<count($arrFields); $i++)
			{
				$class = '';
				$sort = 'asc';
				$strField = strlen($label = $GLOBALS['TL_DCA']['tl_member']['fields'][$arrFields[$i]]['label'][0]) ? $label : $arrFields[$i];

				if ($this->Input->get('order_by') == $arrFields[$i])
				{
					$sort = ($this->Input->get('sort') == 'asc') ? 'desc' : 'asc';
					$class = ' sorted ' . $this->Input->get('sort');
				}

				$arrTh[] = array
				(
					'link' => $strField,
					'href' => (ampersand($strUrl) . $strVarConnector . 'order_by=' . $arrFields[$i]) . '&amp;sort=' . $sort,
					'title' => htmlspecialchars(sprintf($GLOBALS['TL_LANG']['MSC']['list_orderBy'], $strField)),
					'class' => $class . (($i == 0) ? ' col_first' : '')
				);
			}

			$start = -1;
			$limit = $objMember->numRows;

			// TBODY
			while ($objMember->next())
			{
				$publicFields = deserialize($objMember->publicFields, true);
				$class = 'row_' . ++$start . (($start == 0) ? ' row_first' : '') . ((($start + 1) == $limit) ? ' row_last' : '') . ((($start % 2) == 0) ? ' even' : ' odd');

				foreach ($arrFields as $k=>$v)
				{
					$value = '-';

					if ($v == 'username' || in_array($v, $publicFields))
					{
						$value = $this->formatValue($v, $objMember->$v);
					}

					$arrData = $objMember->row();
					unset($arrData['publicFields']);

					$arrTd[$class][$k] = array
					(
						'raw' => $arrData,
						'content' => $value,
						'class' => 'col_' . $k . (($k == 0) ? ' col_first' : ''),
						'id' => $objMember->id,
						'field' => $v
					);
				}
			}

			$this->Template->col_last = 'col_' . ++$k;
			$this->Template->thead = $arrTh;
			$this->Template->tbody = $arrTd;

			// Pagination
			$objPagination = new Pagination($objTotal->count, $per_page);
			$this->Template->pagination = $objPagination->generate("\n  ");
			$this->Template->per_page = $per_page;

			// Template variables
			$this->Template->action = ampersand($this->Environment->request);
			$this->Template->search_label = specialchars($GLOBALS['TL_LANG']['MSC']['search']);
			$this->Template->per_page_label = specialchars($GLOBALS['TL_LANG']['MSC']['list_perPage']);
			$this->Template->search = $this->Input->get('search');
			$this->Template->for = $this->Input->get('for');
			$this->Template->order_by = $this->Input->get('order_by');
			$this->Template->sort = $this->Input->get('sort');
		}
		else
		{
			parent::listAllMembers();
		}
	}
}

?>