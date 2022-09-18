<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\page;

use wcf\data\poll\PollViewableList;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Page with available polls.
 */
class PollsListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 20;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'time';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_FEATUREDPOLL'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.profile.poll.canSeePollPage'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = PollViewableList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['time', 'votes', 'question'];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!WCF::getSession()->getPermission('user.profile.poll.canSeePollPage')) {
            throw new PermissionDeniedException();
        }
    }
}
