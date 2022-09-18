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
namespace wcf\data\poll\featured;

use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents an featured poll.
 */
class FeaturedPoll extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'featuredID';

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'poll_featured';

    /**
     * returns the featured entry
     */
    public static function getFeaturedRow()
    {
        $sql = "SELECT        featuredID
                FROM        wcf" . WCF_N . "_poll_featured
                ORDER BY    featuredID ASC";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        if (!$id = $statement->fetchColumn()) {
            $objectAction = new FeaturedPollAction([], 'create', [
                'data' => [
                    'pollIDs' => \serialize([]),
                ], ]);
            $objectAction->executeAction();
            $returnValues = $objectAction->getReturnValues();
            $id = $returnValues['returnValues']->featuredID;
        }

        return new self($id);
    }
}
