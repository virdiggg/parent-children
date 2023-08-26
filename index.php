<?php

if (!function_exists('parseChildren')) {
	/**
	 * Parse parent-children relationship (recursive)
	 * 
	 * @param array      $array
	 * @param string     $head_key      Primary key, id kepala untuk kepalanya. Kalo paling atas harusnya value $parent_id_val = null.
	 * @param string     $parent_id_val Foreign Key, id kepala untuk bawahannya. Kalo paling atas harusnya value ini null.
	 * @param string     $children_val  Nama value children-nya
	 * @param string|int $parentId
	 *
	 * @return array
	 */
	function parseChildren($array, $head_key = 'id', $parent_id_val = 'parent_id', $children_val = 'childrens', $parentId = 0)
	{
		$result = [];

		if (count($array) === 0) return $result;

		foreach ($array as $key => $value) {
			if ($value[$parent_id_val] == $parentId) {
				$children = parseChildren($array, $head_key, $parent_id_val, $children_val, $value[$head_key]);

				$value[$children_val] = $children ? $children : [];

				$result[] = $value;
			}
		}

		return $result;
	}
}

if (!function_exists('parseChildrenAlt')) {
	/**
	 * Parse parent-children relationship (looping)
	 * 
	 * @param array  $array
	 * @param string $head_key      Primary key, id kepala untuk kepalanya. Kalo paling atas harusnya value $parent_id_val = null.
	 * @param string $parent_id_val Foreign Key, id kepalanya untuk bawahannya. Kalo paling atas harusnya value ini null.
	 * @param string $children_val  Nama value children-nya
	 *
	 * @return array
	 */
	function parseChildrenAlt($array, $head_key = 'id', $parent_id_val = 'parent_id', $children_val = 'childrens')
	{
		$result = [];
		if (count($array) === 0) return $result;

		$array = array_column($array, null, 'id');

		$tree = [];
		foreach($array as &$value) {
			if ($parent = isset($value[$parent_id_val]) ? $value[$parent_id_val] : NULL) {
				$array[$parent][$children_val][] = &$value;
			} else {
				$tree[] = &$value;
			}
		}

		unset($value);
		$array = $tree;
		unset($tree);

		$result = [];
		for($j = 0; $j < count($array); $j++) {
			if (isset($array[$j][$children_val]) && !isset($array[$j][$head_key])) {
				$result = array_merge($result, $array[$j][$children_val]);
			} else {
				$result[] = $array[$j];
			}
		}

		array_multisort(array_column($result, $parent_id_val), array_column($result, $head_key), SORT_ASC, $result);
		return $result;
	}
}
