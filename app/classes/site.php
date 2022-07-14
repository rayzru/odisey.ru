<?php

namespace odissey;

class Site
{

	/**
	 * @var string    Page Title
	 */
	public $title = '';

	/**
	 * @var array    Page Meta Keywords
	 */
	public $keywords = [];

	/**
	 * @var string    Page Meta Description
	 */
	public $description = '';

	/**
	 * @var string    Page canonical link
	 */
	public $canonical = '';

	/**
	 * @var string    Page canonical link
	 */
	public $image = '';

	/**
	 * @var array    Page script sources
	 */
	public $scripts = [];

	/**
	 * @var string    Template name
	 */
	public $template = 'pages/index';

	/**
	 * @var array    Page style sources
	 */
	public $styles = [];

	/**
	 * @var array    Page style sources
	 */
	public $breadcrumbs = [];

	/**
	 * Site constructor.
	 */
	public function __construct() {
	}

	/**
	 * Add script
	 *
	 * @param string $src
	 * @param bool   $head
	 * @param bool   $async
	 * @param bool   $defer
	 */
	public function addScript($src, $head = false, $async = false, $defer = false) {
		if (!empty($src)) {
			$this->scripts[] = ['src' => $src, 'inHead' => $head, 'async' => $async, 'defer' => $defer];
		}
	}

	/**
	 * Add Style source
	 *
	 * @param string $src
	 * @param string $media
	 */
	public function addStyle($src, $media = 'screen') {
		$this->styles[] = [
			'src'   => $src,
			'media' => $media
		];
	}

	/**
	 * Set Page Description
	 *
	 * @param string $description
	 */
	public function setDescription($description = '') {
		$this->description = $description;
	}

	/**
	 * Set Page Canonical Link URL
	 *
	 * @param string $canonical
	 */
	public function setCanonical($canonical = '') {
		$this->canonical = $canonical;
	}


	/**
	 * Set Page Image URL
	 *
	 * @param string $src
	 */
	public function setImage($src = '') {
		$this->image = $src;
	}


	/**
	 * Get Page Description
	 *
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set Page title
	 *
	 * @param string $title
	 *
	 */
	public function setTitle($title = '') {
		$this->title = $title;
	}

	/**
	 * Get Page Title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Add single keyword
	 *
	 * @param string $keyword Keyword string
	 *
	 * @return bool
	 */
	public function addKeyword($keyword) {
		if (in_array($keyword, $this->keywords, true)) {
			return false;
		}

		$this->keywords[] = $keyword;
		return true;
	}

	/**
	 * Add multiple keywords
	 *
	 * @param array $keywords Array of keywords
	 *
	 * @return bool Added status
	 */
	public function addKeywords($keywords) {
		if (is_array($keywords) && sizeof($keywords) === 0) {
			return false;
		}

		// make flat array from array of objects
		if (is_array($keywords) && isset($keywords[0]['keyword'])) {
			$keywords = array_map(function ($k) {
				return $k['keyword'];
			}, $keywords);
		}

		foreach ($keywords as $keyword) {
			$this->addKeyword($keyword);
		}
		return true;
	}

	public function clearKeywords() {
		$this->keywords = [];
	}

	public function addBreadcrumb($title, $url = null) {
		$this->breadcrumbs[] = [
			'title' => $title,
			'url'   => $url
		];
		return true;
	}

	/**
	 * Add multiple breadcrumbs items
	 *
	 * @param $array
	 *
	 * @return bool
	 */
	public function addBreadcrumbs($array) {
		foreach ($array as $bc) {
			if (isset($bc['title']) && isset($bc['url']) && !empty($bc['title'])) {
				$this->addBreadcrumb($bc['title'], $bc['url']);
			} else {
				return false;
			}
		}
		return true;
	}
}
