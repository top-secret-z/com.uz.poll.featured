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
namespace wcf\data\poll;

use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * Represents a list of viewable polls.
 */
class PollViewableList extends PollList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'poll.time DESC';

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->getConditionBuilder()->add('poll.objectID <> ?', [0]);
        $this->getConditionBuilder()->add('poll.objectTypeID <> ?', [0]);
    }

    /**
     * @inheritDoc
     */
    public function countObjects()
    {
        $sql = "SELECT    COUNT(*)
                FROM    wcf" . WCF_N . "_poll poll
                " . $this->getConditionBuilder();
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($this->getConditionBuilder()->getParameters());

        return $statement->fetchColumn();
    }

    /**
     * @inheritDoc
     */
    public function readObjects()
    {
        $sql = "SELECT    poll.*
                FROM    wcf" . WCF_N . "_poll poll
                " . $this->getConditionBuilder() . "
                " . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
        $statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
        $statement->execute($this->getConditionBuilder()->getParameters());
        $this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));

        // add to objects
        $objects = [];
        foreach ($this->objects as $object) {
            $pollObject = PollManager::getInstance()->getRelatedObject($object);

            // simple status to display 0 = not voted, 1 = voted, 2 = forbidden
            $object->status = 2;

            if ($pollObject) {
                // check read permission; std in WL apps
                if (\method_exists($pollObject, 'canRead') && $pollObject->canRead()) {
                    $object->setRelatedObject($pollObject);

                    if ($object->isParticipant()) {
                        $object->status = 1;
                    } elseif ($object->canVote()) {
                        $object->status = 0;
                    }
                }
            }

            // get link, honor status
            $object->link = $object->question;
            $object->hasLink = 0;

            if ($pollObject && \method_exists($pollObject, 'getLink') && $object->status != 2) {
                $object->link = $pollObject->getLink();
                $object->hasLink = 1;
            }

            $objectID = $object->{$this->getDatabaseTableIndexName()};
            $objects[$objectID] = $object;

            $this->indexToObject[] = $objectID;
        }

        $this->objectIDs = $this->indexToObject;
        $this->objects = $objects;
    }
}
