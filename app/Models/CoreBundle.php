<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use DB, Exception;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model,
    Relations\HasMany
};

/**
 * IXP\Models\CoreBundle
 *
 * @property int $id
 * @property string $description
 * @property int $type
 * @property string $graph_title
 * @property int $bfd
 * @property string|null $ipv4_subnet
 * @property string|null $ipv6_subnet
 * @property int $stp
 * @property int|null $cost
 * @property int|null $preference
 * @property int $enabled
 * @property-read Collection|\IXP\Models\CoreLink[] $corelinks
 * @property-read int|null $corelinks_count
 * @method static Builder|CoreBundle newModelQuery()
 * @method static Builder|CoreBundle newQuery()
 * @method static Builder|CoreBundle query()
 * @method static Builder|CoreBundle whereBfd($value)
 * @method static Builder|CoreBundle whereCost($value)
 * @method static Builder|CoreBundle whereDescription($value)
 * @method static Builder|CoreBundle whereEnabled($value)
 * @method static Builder|CoreBundle whereGraphTitle($value)
 * @method static Builder|CoreBundle whereId($value)
 * @method static Builder|CoreBundle whereIpv4Subnet($value)
 * @method static Builder|CoreBundle whereIpv6Subnet($value)
 * @method static Builder|CoreBundle wherePreference($value)
 * @method static Builder|CoreBundle whereStp($value)
 * @method static Builder|CoreBundle whereType($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle active()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereUpdatedAt($value)
 */
class CoreBundle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corebundles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'type',
        'graph_title',
        'bfd',
        'ipv4_subnet',
        'stp',
        'cost',
        'preference',
        'enabled'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stp'         => 'boolean',
    ];

    /**
     * CONST TYPES
     */
    public const TYPE_ECMP              = 1;
    public const TYPE_L2_LAG            = 2;
    public const TYPE_L3_LAG            = 3;

    /**
     * Array STATES
     */
    public static $TYPES = [
        self::TYPE_ECMP          => "ECMP",
        self::TYPE_L2_LAG        => "L2-LAG (e.g. LACP)",
        self::TYPE_L3_LAG        => "L3-LAG",
    ];

    /**
     * Get the corelinks that belong to the corebundle
     */
    public function corelinks(): HasMany
    {
        return $this->HasMany(CoreLink::class, 'core_bundle_id' );
    }

    /**
     * Is the type TYPE_ECMP?
     *
     * @return bool
     */
    public function typeECMP(): bool
    {
        return $this->type === self::TYPE_ECMP;
    }

    /**
     * Is the type isTypeL2Lag?
     *
     * @return bool
     */
    public function typeL2Lag(): bool
    {
        return $this->type === self::TYPE_L2_LAG;
    }

    /**
     * Is the type isTypeL3Lag?
     *
     * @return bool
     */
    public function typeL3Lag(): bool
    {
        return $this->type === self::TYPE_L3_LAG;
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     *
     * @return string
     */
    public function typeText(): string
    {
        return self::$TYPES[ $this->type ] ?? 'Unknown';
    }

    /**
     * Return all active core bundles
     *
     * @param Builder $query
     *
     * @return Builder
     */

    public function scopeActive( Builder $query ): Builder
    {
        return $query->where( 'enabled' , true )
            ->orderBy( 'description' );
    }

    /**
     * get switch from side A or B
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return Switcher|bool
     */
    public function switchSideX( bool $sideA = true )
    {
        if( $cl = $this->corelinks()->first() ){
            /** @var CoreInterface $side */
            $side = $sideA ? $cl->coreInterfaceSideA : $cl->coreInterfaceSideB ;
            return $side->physicalinterface->switchPort->switcher;
        }

        return false;
    }

    /**
     * Check if all the core links for the core bundle are enabled
     *
     * @return boolean
     */
    public function allCoreLinksEnabled(): bool
    {
        return $this->corelinks->where( 'enabled', false )->count() <= 0;
    }

    /**
     * get the speed of the Physical interface
     *
     * @return int
     */
    public function speedPi(): int
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->speed;
        }
        return 0;
    }

    /**
     * get the duplex of the Physical interface
     *
     * @return int|false
     */
    public function duplexPi()
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->duplex;
        }

        return false;
    }

    /**
     * get the auto neg of the Physical interface
     *
     * @return int|false
     */
    public function autoNegPi()
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->autoneg;
        }

        return false;
    }

    /**
     * get the customer associated virtual interface of the core bundle
     *
     * @return Customer|bool
     */
    public function customer()
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->virtualInterface->customer;
        }
        return false;
    }

    /**
     * get the virtual interfaces linked to the core links of the side A and B
     *
     * @return array
     */
    public function virtualInterfaces(): array
    {
        $vis = [];
        if( $cl = $this->corelinks()->first() ){
            $vis[ 'a' ] = $cl->coreInterfaceSideA->physicalInterface->virtualInterface;
            $vis[ 'b' ] = $cl->coreInterfaceSideB->physicalInterface->virtualInterface;
        }
        return $vis;
    }

    /**
     * Check if the switch is the same for the Physical interfaces of the core links associated to the core bundle
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return bool
     */
    public function sameSwitchForEachPIFromCL( bool $sideA = true ): bool
    {
        $switches = [];

        foreach( $this->corelinks as $cl ) {
            /** @var CoreInterface $side */
            $side = $sideA ? $cl->coreInterfaceSideA : $cl->coreInterfaceSideB ;
            $switches[] = $side->physicalInterface->switchPort->switcher->id;
        }

        return count(array_unique($switches)) == 1;
    }

    /**
     * Delete the Core Bundle ans everything related.
     *
     * @return bool
     *
     * @throws
     */
    public function deleteObject(): bool
    {
        try {
            DB::beginTransaction();

            foreach( $this->corelinks as $cl ){
                $cl->delete();
                foreach( $cl->coreInterfaces() as $ci ){
                    /** @var CoreInterface  $ci */
                    $ci->delete();
                    $ci->physicalInterface->virtualInterface->delete();
                    $ci->physicalInterface->delete();
                }
            }
            $this->delete();

            DB::commit();

        } catch( Exception $e ) {
            DB::rollBack();
            throw $e;
        }

        return true;
    }
}
