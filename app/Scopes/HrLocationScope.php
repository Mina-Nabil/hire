<?php

namespace App\Scopes;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class HrLocationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // If no user is logged in or if they are admin, don't restrict
        if (!$user || $user->is_admin) {
            return;
        }

        // If user is HR, restrict to their assigned locations
        if ($user->is_hr) {
            $locationIds = $user->assignedLocations()->pluck('locations.id')->toArray();
            
            // Only apply filter if the user has assigned locations
            if (!empty($locationIds)) {
                // Different handling based on model type
                $table = $builder->getModel()->getTable();
                
                if ($table === 'locations') {
                    // For Location model, filter by its own ID
                    $builder->whereIn('id', $locationIds);
                } else {
                    // For Position model or others, filter by location_id
                    $builder->whereIn('location_id', $locationIds);
                }
            }
        }
    }
    
    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        $builder->macro('withAllHierarchy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
        
        $builder->macro('withHrAccessibleParents', function (Builder $builder) {
            // Get the currently authenticated user
            $user = Auth::user();
            
            // If no user is logged in or if they are admin, don't add special handling
            if (!$user || $user->is_admin) {
                return $builder;
            }
            
            // For HR users, include parent positions even if they're not in their locations
            if ($user->is_hr) {
                $locationIds = $user->assignedLocations()->pluck('locations.id')->toArray();
                
                if (!empty($locationIds)) {
                    $table = $builder->getModel()->getTable();
                    
                    if ($table === 'locations') {
                        return $builder->whereIn('id', $locationIds);
                    } else {
                        return $builder->where(function ($query) use ($locationIds) {
                            $query->whereIn('location_id', $locationIds)
                                ->orWhereHas('children', function ($childQuery) use ($locationIds) {
                                    $childQuery->whereIn('location_id', $locationIds);
                                });
                        });
                    }
                }
            }
            
            return $builder;
        });
    }
} 