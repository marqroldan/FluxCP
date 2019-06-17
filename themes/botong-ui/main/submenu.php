<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php $subMenuItems = $this->getSubMenuItems(); $menus = array() ?>
<?php if (!empty($subMenuItems)): ?>
	<?php foreach ($subMenuItems as $menuItem): ?>
		<?php $menus[] = sprintf('<a href="%s" class="dropdown-item %s">%s</a>',
			$this->url($menuItem['module'], $menuItem['action']),
			$params->get('module') == $menuItem['module'] && $params->get('action') == $menuItem['action'] ? ' active' : '',
			htmlspecialchars($menuItem['name'])) ?>
	<?php endforeach ?>
	<?php echo implode('', $menus) ?>
<?php endif ?>