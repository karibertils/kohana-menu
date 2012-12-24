<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Represents a single menu item (typically a link) in the Menu.
 *
 * @author Ando Roots <ando@sqroot.eu>
 * @since 2.0
 */
class Kohana_Menu_Item
{

	/**
	 * @var array Current item config
	 * @since 2.0
	 */
	private $_config = [
		'classes'  => [], // Extra classes for this menu item
		'icon'     => NULL, // Icon class for this menu item
		'siblings' => [], // Sub-links
		'title'    => NULL, // Visible text
		'tooltip'  => NULL, // Tooltip text for this menu item
		'url'      => '#', // Relative or absolute target for this menu item (href)
		'visible'  => TRUE
	];

	/**
	 * @var Menu
	 */
	private $_menu;

	/**
	 * @param array $item_config Menu item config
	 * @param \Menu $menu The menu where this item belongs
	 * @since 2.0
	 */
	public function __construct(array $item_config, Menu $menu)
	{
		$this->_menu = $menu;

		// Save item config options. Defaults are retained if not present in $item_config
		$this->_config = array_replace($this->_config, $item_config, ['items' => []]);

		// Apply URL::site
		if (! 'http://' == substr($this->_config['url'], 0, 7)    AND ! 'https://' == substr($this->_config['url'], 0, 8)) {
			$this->_config['url'] = URL::site($this->_config['url']);
		}

		// Sub-menu
		if (array_key_exists('items', $item_config) && count($item_config['items']) > 0) {
			foreach ($item_config['items'] as $sibling) {
				$this->_config['siblings'][] = new Menu_Item($sibling, $menu);
			}
		}
	}

	/**
	 * @return mixed Rendered menu item content (typically a link)
	 */
	public function __toString()
	{
		return HTML::anchor(
			$this->_config['url'],
			$this->_config['title'],
			[
				'title' => $this->_config['tooltip']
			],
			NULL,
			FALSE
		);
	}

	/**
	 * @since 2.0
	 * @return bool Whether the current menu item has siblings (sub-items)
	 */
	public function has_siblings()
	{
		return count($this->_config['siblings']) > 0;
	}

	/**
	 * Add a CSS class to the current item
	 *
	 * @since 2.0
	 * @param string $class
	 * @return Kohana_Menu_Item
	 */
	public function add_class($class)
	{
		if (! in_array($class, $this->_config['classes'])) {
			$this->_config['classes'][] = $class;
		}
		return $this;
	}

	/**
	 * Remove a CSS class from the current menu item
	 *
	 * @since 2.0
	 * @param string $class
	 * @return Kohana_Menu_Item
	 */
	public function remove_class($class)
	{
		if ($key = array_search($class, $this->_config['classes'])) {
			unset($this->_config['classes'][$key]);
		}
		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_visible()
	{
		return $this->_config['visible'];
	}

	/**
	 * @since 2.0
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->_config)) {
			return $this->_config[$name];
		}
	}

}