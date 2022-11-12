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
namespace wcf\system\event\listener;

use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\featured\FeaturedPollAction;

/**
 * Listen to Poll actions for Featured Poll
 */
class FeaturedPollListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        // only if configured
        if (!MODULE_FEATUREDPOLL) {
            return;
        }

        // only action create
        $action = $eventObj->getActionName();
        if ($action != 'create') {
            return;
        }

        // get poll
        $returnValues = $eventObj->getReturnValues();
        $pollID = $returnValues['returnValues']->pollID;

        // get featured and add poll, if configured
        $featured = FeaturedPoll::getFeaturedRow();
        if (!$featured->autoAdd) {
            return;
        }

        $featuredPollIDs = \unserialize($featured->pollIDs);
        $featuredPollIDs[] = $pollID;

        // save and set to new poll
        $objectAction = new FeaturedPollAction([$featured->featuredID], 'update', [
            'data' => [
                'pollIDs' => \serialize($featuredPollIDs),
                'actualPollID' => $pollID,
            ],
        ]);
        $objectAction->executeAction();
    }
}
