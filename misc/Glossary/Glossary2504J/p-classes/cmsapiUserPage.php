<?php

class cmsapiUserPage {
	var $baseurl = '';
	var $itemcount = 0;
	var $itemsperpage = 10;
	var $startItem = 1;
	var $currentpage = 1;
	var $pagetotal = 1;
	var $itemid = 1;
	var $countshown = false;

	function cmsapiUserPage ($itemcount, &$remUser, $itemsperpage, $page, $querystring) {
		$interface =& cmsapiInterface::getInstance();
		$this->itemid = $interface->getCurrentItemid();
		$cname = strtolower(_THIS_COMPONENT_NAME);
		$this->baseurl = "index.php?option=com_$cname&Itemid={$this->itemid}{$querystring}&page=";
		$this->itemcount = $itemcount;
		$this->itemsperpage = $itemsperpage;
		$this->startItem = 1;
		$this->finishItem = $itemsperpage;
		$this->pagetotal = ceil($this->itemcount/$this->itemsperpage);
		$this->setPage($page);
	}

	function setPage ($currentpage) {
		$this->currentpage = $currentpage;
		$basecount = ($currentpage - 1) * $this->itemsperpage;
		$this->startItem = $basecount;
	}

	function pageTitle ($page, $special=null) {
		echo 'title="';
		if ($special) echo $special;
		else echo _CMSAPI_PAGE_SHOW_RESULTS;
		$finish = $page * $this->itemsperpage;
		$start = $finish - $this->itemsperpage + 1;
		if ($finish > $this->itemcount) $finish = $this->itemcount;
		printf (_CMSAPI_PAGE_SHOW_RANGE, $start, $finish, $this->itemcount).'"';
	}

	function showNavigation ($pagespread) {
		$interface =& cmsapiInterface::getInstance();
		if ($this->itemcount <= $this->itemsperpage) return;
		$lowpage = max(1,intval($this->currentpage - ($pagespread+1)/2));
		$highpage = $lowpage + $pagespread;
		if ($highpage > $this->pagetotal) {
			$lowpage = max(1, $lowpage - ($highpage-$this->pagetotal));
			$highpage = $this->pagetotal;
		}
		$previous = $this->currentpage - 1;
		if ($previous) {
			$url = $interface->sefRelToAbs($this->baseurl.$previous);
			$prevtext = _CMSAPI_PREVIOUS;
			$previouslink = <<<PREVIOUS_LINK
			<a href="$url">$prevtext</a>
PREVIOUS_LINK;
			$url = $interface->sefRelToAbs($this->baseurl.'1');
			$startlink = <<<START_LINK
			<a href="$url">&laquo;</a>
START_LINK;
		}
		else $previouslink = $startlink = '';
		$page = $lowpage;
		if ($page > 1) $navdetails = '...';
		else $navdetails = '';
		$spacer = '';
		while ($page <= $highpage) {
			if ($page == $this->currentpage) {
				$navdetails .= $spacer.$page;
			}
			else {
				$url = $interface->sefRelToAbs ($this->baseurl.$page);
				$navdetails .= <<<NAV_DETAIL
				<a href="$url">$page</a>
NAV_DETAIL;
			}
			$spacer = ' ';
			$page++;
		}

		if ($page <= $this->pagetotal) $navdetails .= '...';
		$next = $this->currentpage + 1;

		if ($next <= $this->pagetotal) {
			$url = $interface->sefRelToAbs($this->baseurl.$next);
			$nexttext = _CMSAPI_NEXT;
			$nextlink = <<<NEXT_LINK
			<a href="$url">$nexttext</a>
NEXT_LINK;
			$url = $interface->sefRelToAbs($this->baseurl.$this->pagetotal);
			$lastlink = <<<LAST_LINK
			<a href="$url">&raquo;</a>
LAST_LINK;
		}
		else $nextlink = $lastlink = '';

		$pagetext = _CMSAPI_PAGE_TEXT;
		if (!$this->countshown) {
			// $count_control = $this->showPageCount();
			// If used, add $count_control after first div below
			$this->countshown = true;
			return <<<BIG_NAVIGATION

			<div class="cmsapipagecontrols">
			<div class='cmsapipagenav'>
				<strong>$pagetext:&nbsp;</strong>
				$startlink $previouslink $navdetails $nextlink $lastlink
			<!-- End of cmsapipagenav -->
			</div>
			<div class="cmsapipagecontrolsend"></div>
			<!-- End of cmsapipagecontrols -->
			</div>

BIG_NAVIGATION;

		}
		else return <<<NAVIGATION

		<div class="cmsapifilelistingfooter">
		<div class='cmsapipagenav'>
			<strong>$pagetext:&nbsp;</strong>
			$startlink $previouslink $navdetails $nextlink $lastlink
		<!-- End of cmsapipagenav -->
		</div>
		</div>

NAVIGATION;

	}

	function startItem () {
		return $this->startItem;
	}
	
}