<?php

namespace App\Helper;
use Validator;

trait DataViewer
{

	

    public static function scopeSearchPaginateAndOrder($query)
    {

    	$operators = [
			'equal' => '=',
			'not_equal' => '<>',
			'less_than' => '<',
			'greater_than' => '>',
			'less_than_or_equal_to' => '<=',
			'greater_than_or_equal_to' => '>=',
			'like' => 'LIKE',
			'in' => 'IN'
		];

    	$request = app()->make('request');

    	$v = Validator::make($request->only([
    			'column','direction','per_page','search_column','search_operator','search_term'
    		]),[
    			'column' => 'required|alpha_dash|in:'.implode(',', self::$columns),
    			'direction' => 'required|in:asc,desc',
    			'per_page' => 'integer|min:1|max:100',
    			'search_column' => 'required|alpha_dash|in:'.implode(',', self::$columns),
    			'search_operator' => 'required|alpha_dash|in:'.implode(',', array_keys($operators) ),
    			'search_term' => 'max:255'
    		]);

    	if($v->fails()){
    		dd($v->messages());
    	}

    	if($request['search_operator'] == 'like'){
    		$request['search_term'] = '%'.$request['search_term'].'%';
    	}

    	if($request['search_operator'] == 'in'){
    		$query->whereIn($request['search_column'], explode(',', $request['search_term']));
    	}
		else if( $request['search_term'] != '%%'){
    		$query->where($request['search_column'],$operators[$request['search_operator']],$request['search_term']);
    	}

        return $query->orderBy($request['column'],$request['direction'])->paginate($request['per_page']);
    }
}


?>