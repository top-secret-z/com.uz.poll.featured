<?php
namespace wcf\system\page\handler;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;

/**
 * Provides the polls page.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class PollsListPageHandler extends AbstractMenuPageHandler {
	/**
	 * @inheritDoc
	 */
	public function isVisible($objectID = null) {
		if (!WCF::getSession()->getPermission('user.profile.poll.canSeePollPage')) {
			return false;
		}
		
		return true;
	}
}
