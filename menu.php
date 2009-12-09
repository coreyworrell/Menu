<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Menu Builder
 *
 * This class can be used to easily build out a menu in the form
 * of an unordered list. You can add any attributes you'd like to
 * the main list, and each list item has special classes to help
 * you style it.
 *
 * @author   Corey Worrell
 * @homepage http://coreyworrell.com
 * @version  1.1
 */

class Menu {

	// Associative array of list items
	public $items = array();
	
	// Associative array of attributes for list
	public $attrs = array();
	
	// Current URI
	protected $current;
	
	/**
	 * Creates and returns a new menu object
	 *
	 * @chainable
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  Menu
	 */
	public static function factory(array $items = NULL)
	{
		return new Menu($items);
	}
	
	/**
	 * Constructor, globally sets $items array
	 *
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  void
	 */
	public function __construct(array $items = NULL)
	{
		$this->items   = $items;
		$this->current = trim(URL::site(Request::instance()->uri()), '/');
	}
	
	/**
	 * Add's a new list item to the menu
	 *
	 * @chainable
	 * @param   string   Title of link
	 * @param   string   URL (address) of link
	 * @param   Menu     Instance of class that contain children
	 * @return  Menu
	 */
	public function add($title, $url, Menu $children = NULL)
	{
		$this->items[] = array
		(
			'title'    => $title,
			'url'      => $url,
			'children' => ($children instanceof Menu) ? $children->items : NULL,
		);
		
		return $this;
	}
	
	/**
	 * Renders the HTML output for the menu
	 *
	 * @param   array   Associative array of html attributes
	 * @param   array   The parent item's array, only used internally
	 * @return  string  HTML unordered list
	 */
	public function render(array $attrs = NULL, array $items = NULL)
	{
		static $i;
		
		$items = empty($items) ? $this->items : $items;
		$attrs = empty($attrs) ? $this->attrs : $attrs;
		
		$i++;
		
		if ($i !== 1)
		{
			$attrs = array();
		}
		
		$attrs['class'] = empty($attrs['class']) ? 'level-'.$i : $attrs['class'].' level-'.$i;
		
		$menu = '<ul'.HTML::attributes($attrs).'>';
		
		foreach ($items as $key => $item)
		{
			$has_children = isset($item['children']);
			
			$classes = NULL;
			
			if ($has_children)
			{
				$classes[] = 'parent';
			}
			if ($active = $this->active($item))
			{
				$classes[] = $active;
			}
			if ( ! empty($classes))
			{
				$classes = HTML::attributes(array('class' => implode(' ', $classes)));
			}
			
			$menu .= '<li'.$classes.'>'.HTML::anchor($item['url'], $item['title']);
			if ($has_children)
			{
				$menu .= $this->render(NULL, $item['children']);
			}
			$menu .= '</li>';
		}
		
		$menu .= '</ul>';
		
		$i--;
		
		return $menu;
	}
	
	/**
	 * Determines if the menu item is part of the current URI
	 *
	 * @param   array   The item to check against
	 * @return  mixed   Returns active class or null
	 */
	private function active(array $item)
	{
		$link = trim(URL::site($item['url']), '/');
		
		// Exact match (removes default 'index' action)
		if ($this->current === $link OR preg_replace('~/?index/?$~', '', $this->current) === $link)
		{
			return 'active current';
		}
		// Checks if it is part of the active path
		else
		{
			$current_pieces = explode('/', $this->current); array_shift($current_pieces);
			$link_pieces    = explode('/', $link);          array_shift($link_pieces);
			
			for ($i = 0, $l = count($link_pieces); $i < $l; $i++)
			{
				if ((isset($current_pieces[$i]) AND $current_pieces[$i] !== $link_pieces[$i]) OR empty($current_pieces[$i]))
				{
					return;
				}
			}
			
			return 'active';
		}
	}
	
	/**
	 * Renders the HTML output for menu without any attributes or active item
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * Easily set list attributes
	 *
	 * @param   mixed   Value to set to
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->attrs[$key] = $value;
	}
	
	/**
	 * Get a list attribute
	 *
	 * @return   mixed   Value of key
	 */
	public function __get($key)
	{
		if (isset($this->attrs[$key]))
		{
			return $this->attrs[$key];
		}
	}
	
	/**
	 * Nicely outputs contents of $this->items for debugging info
	 *
	 * @return   string
	 */
	public function debug()
	{
		return Kohana::debug($this->items);
	}

}