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

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * Infrastructure
 */
class Infrastructure
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $shortname;

    /**
     * @var string
     */
    protected $country;



    /**
     * @var integer
     */
    protected $id;

    protected $created_at;
    protected $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Switchers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Vlans;



    /**
     * The ID of the IX as generated by PeeringDB
     * @var int
     */
    protected $peeringdb_ix_id = null;

    /**
     * The ID of the IX as generated by IX-F
     * @var int
     */
    protected $ixf_ix_id = null;


    /**
     * Set name
     *
     * @param string $name
     * @return Infrastructure
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set shortname
     *
     * @param string $shortname
     * @return Infrastructure
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switchers = new ArrayCollection();
    }

    /**
     * Add Switchers
     *
     * @param Switcher $switchers
     * @return Infrastructure
     */
    public function addSwitcher(Switcher $switchers)
    {
        $this->Switchers[] = $switchers;

        return $this;
    }

    /**
     * Remove Switchers
     *
     * @param Switcher $switchers
     */
    public function removeSwitcher(Switcher $switchers)
    {
        $this->Switchers->removeElement($switchers);
    }

    /**
     * Get Switches
     *
     * 20160401 - added new parameters to limit the switches returned. These
     * are null to ensure expected behavior for code written pre this change.
     *
     * @param bool $active Limit to (in)active switches
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection|array
     */
    public function getSwitchers( $active = null )
    {
        if( $active === null ) {
            return $this->Switchers;
        }

        $sws = [];

        foreach( $this->Switchers as $v => $s ) {
            if( $active === null || $s->getActive() == $active ) {
                $sws[$v] = $s;
            }
        }

        return $sws;
    }

    /**
     * Add Vlans
     *
     * @param Vlan $vlans
     * @return Infrastructure
     */
    public function addVlan(Vlan $vlans)
    {
        $this->Vlans[] = $vlans;

        return $this;
    }

    /**
     * Remove Vlans
     *
     * @param Vlan $vlans
     */
    public function removeVlan(Vlan $vlans)
    {
        $this->Vlans->removeElement($vlans);
    }

    /**
     * Get Vlans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVlans()
    {
        return $this->Vlans;
    }

    /**
     * @var boolean
     */
    private $isPrimary;


    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     * @return Infrastructure
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    /*
     * Get isPrimary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }



    /**
     * PeeringDB generates IDs for IXs
     *
     * @see https://www.peeringdb.com/api/ix
     * @see https://www.peeringdb.com/apidocs/#!/ix
     * @return int
     */
    public function getPeeringdbIxId() {
        return $this->peeringdb_ix_id;
    }

    /**
     * PeeringDB generates IDs for IXs
     *
     * @see https://www.peeringdb.com/api/ix
     * @see https://www.peeringdb.com/apidocs/#!/ix
     * @param int $id
     * @return \Entities\Infrastructure
     */
    public function setPeeringdbIxId( $id ): Infrastructure {
        $this->peeringdb_ix_id = $id;
        return $this;
    }


    /**
     * IX-F generates IDs for IXs
     *
     * @see https://db.ix-f.net/api/ixp
     *
     * @return int
     */
    public function getIxfIxId() {
        return $this->ixf_ix_id;
    }

    /**
     * IX-F generates IDs for IXs
     *
     * @see https://db.ix-f.net/api/ixp
     *
     * @param int $id
     * @return \Entities\Infrastructure
     */
    public function setIxfIxId( $id ): Infrastructure {
        $this->ixf_ix_id = $id;
        return $this;
    }


    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Infrastructure
     */
    public function setCountry( string $country ): Infrastructure
    {
        $this->country = $country;
        return $this;
    }

}
