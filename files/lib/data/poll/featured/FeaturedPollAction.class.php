<?php
namespace wcf\data\poll\featured;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes featured poll related actions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = FeaturedPollEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.display.canManageFeaturedPoll'];
	protected $permissionsUpdate = ['admin.display.canManageFeaturedPoll'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['delete', 'toggle', 'update'];
}
