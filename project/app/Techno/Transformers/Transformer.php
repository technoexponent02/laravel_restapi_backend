<?php namespace Techno\Transformers;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Transformer {

    public function transformCollection($items)
    {
        return array_map([$this, 'transform'], is_array($items)? $items : $items->toArray());
    }

    public abstract function transform($item);
    
}