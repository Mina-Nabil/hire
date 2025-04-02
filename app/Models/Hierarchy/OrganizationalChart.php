<?php

namespace App\Models\Hierarchy;

use Illuminate\Support\Collection;

class OrganizationalChart
{
    const MORPH_NAME = 'organizational_chart';
    
    /**
     * Get the top-level positions (positions without parents)
     *
     * @return Collection
     */
    public static function getTopLevelPositions(): Collection
    {
        return Position::whereNull('parent_id')->get();
    }

    /**
     * Get the complete organizational hierarchy as a nested structure
     *
     * @return array
     */
    public static function getHierarchy(): array
    {
        $topPositions = self::getTopLevelPositions();
        
        return $topPositions->map(function ($position) {
            return self::buildHierarchyTree($position);
        })->toArray();
    }

    /**
     * Build a hierarchical tree starting from a position
     *
     * @param Position $position
     * @return array
     */
    public static function buildHierarchyTree(Position $position): array
    {
        $children = $position->children;
        
        $result = [
            'id' => $position->id,
            'name' => $position->name,
            'department' => $position->department->name,
            'employee' => $position->employee ? $position->employee->full_name : null,
        ];
        
        if ($children->isNotEmpty()) {
            $result['children'] = $children->map(function ($child) {
                return self::buildHierarchyTree($child);
            })->toArray();
        }
        
        return $result;
    }

    /**
     * Get all direct and indirect subordinates for a given position
     *
     * @param Position $position
     * @return Collection
     */
    public static function getAllSubordinates(Position $position): Collection
    {
        $subordinates = collect();
        
        foreach ($position->children as $child) {
            $subordinates->push($child);
            $subordinates = $subordinates->merge(self::getAllSubordinates($child));
        }
        
        return $subordinates;
    }

    /**
     * Find the common manager between two positions
     *
     * @param Position $position1
     * @param Position $position2
     * @return Position|null
     */
    public static function findCommonManager(Position $position1, Position $position2): ?Position
    {
        $chain1 = self::getManagerChain($position1);
        $chain2 = self::getManagerChain($position2);
        
        foreach ($chain1 as $manager) {
            if ($chain2->contains('id', $manager->id)) {
                return $manager;
            }
        }
        
        return null;
    }

    /**
     * Get the chain of managers up to the top of the hierarchy
     *
     * @param Position $position
     * @return Collection
     */
    public static function getManagerChain(Position $position): Collection
    {
        $chain = collect();
        $current = $position->parent;
        
        while ($current) {
            $chain->push($current);
            $current = $current->parent;
        }
        
        return $chain;
    }

    /**
     * Check if a position is above another position in the hierarchy
     *
     * @param Position $manager
     * @param Position $subordinate
     * @return bool
     */
    public static function isManager(Position $manager, Position $subordinate): bool
    {
        $chain = self::getManagerChain($subordinate);
        return $chain->contains('id', $manager->id);
    }
} 