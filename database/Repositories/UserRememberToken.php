<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Repositories;

use Illuminate\Support\Facades\Session as SessionFacade;

use Doctrine\ORM\EntityRepository;


/**
 * Remember Tokens
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRememberToken extends EntityRepository
{

    /**
     * Get all api keys for listing on the frontend CRUD
     *
     * @param \stdClass $feParams
     * @param int|null $userid
     * @param int|null $id
     *
     * @return array Array of infrastructures (as associated arrays) (or single element if `$id` passed)
     *
     * @see \IXP\Http\Controllers\Doctrine2Frontend
     */
    public function getAllForFeList( \stdClass $feParams, int $userid, int $id = null )
    {
        $dql = "SELECT  urt.id         AS id, 
                        urt.token      AS token,
                        urt.device     AS device, 
                        urt.ip         AS ip, 
                        urt.created    AS created, 
                        urt.expires    AS expires
                FROM Entities\\UserRememberToken urt
                WHERE urt.User = " . (int)$userid;

        if( $id ) {
            $dql .= " AND urt.id = " . (int)$id;
        }

        if( isset( $feParams->listOrderBy ) ) {
            $dql .= " ORDER BY " . $feParams->listOrderBy . ' ';
            $dql .= isset( $feParams->listOrderByDir ) ? $feParams->listOrderByDir : 'ASC';
        }

        return $this->getEntityManager()->createQuery( $dql )->getArrayResult();
    }


    /**
     * Delete all the Remember token for the user
     *
     * @param int   $userid
     * @param bool  $deleteCurrentToken Do we need to delete the current token
     *
     * @return void
     */
    public function deleteByUser( int $userid, bool $deleteCurrentToken = false )
    {
        $dql = "DELETE FROM Entities\\UserRememberToken urt
                WHERE urt.User = ?1";


        return $this->getEntityManager()->createQuery( $dql )->setParameter(1, $userid )->execute();
    }
}
