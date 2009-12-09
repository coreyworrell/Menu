# Menu

This class can be used within the [Kohana 3 Framework](http://v3.kohanaphp.com) to display nested menus of HTML links.
It takes an array of menu items eventually with nested arrays that define sub-menus.
The class generates HTML lists with links for each menu item.
CSS classes can be used to configure the presentation styles of the menus, regular items and the current page item.

## Basic Usage

	$menu = Menu::factory()
		->add('Home', 'home')
		->add('Company', 'company', Menu::factory()
			->add('About Us', 'company/about')
			->add('Stuff', 'company/stuff', Menu::factory()
				->add('Stuff One', 'company/stuff/one')
				->add('Stuff Two', 'company/stuff/two')
				->add('Stuff Three', 'company/stuff/three'))
			->add('Members', 'company/members'))
		->add('Test Link', 'test');
		
## Adding Attributes

	$attrs = array
	(
		'id'    => 'navigation',
		'class' => 'menu'
	);
	
	echo Menu::factory()
		->add(...)
		...
		->render($attrs);
		
## Using a Database

	// File: /application/classes/models/menu.php
	
	public function build($level = NULL)
	{
		$level_id = empty($level) ? 0 : $level;
		
		$levels = $this->where('parent', '=', $level_id)->orderby('id', 'asc')->find_all();
		
		$menu = new Menu;
		
		foreach ($levels as $lvl)
		{
			$menu->add($lvl->title, $lvl->url, $this->build($lvl->id));
		}
		
		return $menu;
	}