<?php
namespace App\Helpers;



use App\Helpers\SqlHelper;



class DatatableHelper {
	public static function make($cmd = null, $columns = [], $start, $length, $order = [], $search)
	{
		if(!$cmd) {
			throw new \Exception("cmd is required");
		}

        if (!$order) {
            $order['column'] = 'created_at';
            $order['dir'] = 'desc';
        }

        // return [
        //     'start' => $start,
        //     'length' => $length,
        //     'order' => $order,
        //     'search' => $search,
        // ];

        // return $order;



		// $columns = $request->get("columns");


        $search = strtolower($search);
		$totalData = $cmd->count();
		$totalFiltered = $cmd->count();

        // return $search;

        if (!$search) {
            if ($length > 0) {
                $cmd->skip($start)->take($length);
            }

			$cmd->latest('updated_at');
			// foreach ($order as $row) {
			// 	$cmd->orderBy($row['column'], $row['dir']);
			// }
            $cmd->orderBy($order['col'], $order['dir']);


		} else {
            $cmd->where(function($q) use($columns, $search) {
                foreach ($columns as $field) {
                    $q->orWhereRaw("lower($field) like ?", ["%{$search}%"]);
                }
            });

			$totalFiltered = $cmd->count();
			if ($length > 0) {
				$cmd->skip($start)->take($length);
			}

			$cmd->latest('updated_at');
			// foreach ($order as $row) {
			// 	$cmd->orderBy($row['column'], $row['dir']);
			// }
            $cmd->orderBy($order['col'], $order['dir']);

		}




		$rows = $cmd->get();

        $data = [
            'data' => $rows,
            // 'totalData' => $totalData,
            // 'totalFiltered' => $totalFiltered,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            // 'draw' => $draw,
        ];
        if (env('APP_DEBUG')) {
            $data['sql'] = SqlHelper::getSqlWithBindings($cmd);
        }

        return $data;

	}

	/**
	 * @param adalah parameter standard dari datatable
	 * $param[start] = ?
	 * $param[length] = ?
	 * $param[search] = ?
	 * $param[columns] = ? column yang di search
	 * $param[order] = ?
	 * $param[filters] = ?
	 */
	public static function prepareQuery($cmd, $param = [])
	{

		if(!$cmd) {
			throw new \Exception("cmd is required");
		}

		$id = Util::getArr($param, 'id');
		$start = Util::getArr($param, 'start', 0);
		// $draw = Util::getArr($param, 'draw', 0);
		// $draw or $draw = request("draw", 1);

		$length = Util::getArr($param, 'length');
		$search = Util::getArr($param, "search");
		$columns = Util::getArr($param, 'columns');
		$order = Util::getArr($param, 'order', []);
		$filters = Util::getArr($param, 'filters');
		$m2o = Util::getArr($param, 'm2o');

		$search = strtolower($search);
		$filters = Parser::parseFilter($filters);

		$totalData = $cmd->count();

		$sql = Parser::getSqlWithBindings($cmd);

		$cmd = Parser::prepareWhereToModel($cmd, $filters);

		if($id) {
			$cmd->where("id", $id);
		}

		// return [
		// 	'cololum' => $columns
		// ];


		$totalFiltered = $cmd->count();
		$sql2 = Parser::getSqlWithBindings($cmd);

		// $res = [];
		// foreach($columns as $field_name) {
		// 	$rel = [];
		// 	if (strpos($field_name, '.') === false ) {
		// 		$rel = ['column_name' => $field_name];
		// 	}else{
		// 		$relations = array_reverse(explode('.', $field_name));
		// 		$column_name = $relations[0];
		// 		unset($relations[0]);
		// 		$relations = array_reverse($relations);
		// 		$relation = implode('.', $relations);

		// 		$rel = [
		// 			'column_name' => $column_name,
		// 			'relation' => $relation,
		// 		];
		// 	}



		// 	array_push($res, $rel);
		// }

		// return ['res' => $res];

		if (!$search) {
			if ($length > 0) {
				$cmd->skip($start)->take($length);
			}

			$cmd->latest('updated_at');
			foreach ($order as $row) {
				$cmd->orderBy($row['column'], $row['dir']);
			}

		} else {
			$search_bind = "%$search%";
			if($columns) {
				$search_bind = "%$search%";

				$cmd->where(function($cmd) use ($columns, $search_bind, $m2o, $inject_where){
					foreach($columns as $field_name) {
						if (!$field_name) {
							continue;
						}
						if (strpos($field_name, ',') !== false ) {
							continue;
						}
						if (strpos($field_name, '.') === false ) {
							if(in_array($field_name, ['desc', 'to', 'from'])){
								$field_name = '"'.$field_name.'"';
							}
							if ($field_name == "is_active") {
								$is_active = strtolower($search_bind) ==  "%active%" ? true : false;
								$cmd->where($field_name, $is_active);
							}else{
								$field = 'LOWER(CAST('.$field_name.' as varchar))';
								$cmd->orWhereRaw("$field LIKE ?", [$search_bind]);
							}
						}else{
							$relations = array_reverse(explode('.', $field_name));
							$column_name = 'LOWER(CAST('.$relations[0].' as varchar))';
							unset($relations[0]);
							$relations = array_reverse($relations);
							$relation = implode('.', $relations);
							$cmd->orWhereHas($relation, function($q) use($column_name, $search_bind) {
								if(in_array($column_name, ['desc', 'to', 'from'])){
									$column_name = '"'.$column_name.'"';
								}
								$field = 'LOWER(CAST('."$column_name".' as varchar))';
								$q->whereRaw("$field LIKE ?", [$search_bind]);
							});
							// $cmd->orWhereRelationRaw($relation, "lower($column_name) like ?", [$search_bind]);
						}
					}
					if ($inject_where) {
						$cmd->orWhere($inject_where);
					}


					// foreach($columns as $field_name) {
					// 	$field = 'LOWER(CAST('.$field_name.' as varchar))';
					// 	$cmd->orWhereRaw("$field LIKE ?", [$search_bind]);
					// }

					// if ($m2o) {
					// 	foreach($m2o as $one) {
					// 		$fk = $one[0];
					// 		$relation_field_name = $one[1];
					// 		// $cmd->orWhereRelationRaw($fk, "lower($field_name) like ?", [$search_bind]);
					// 		// $cmd->orWhereHas($fk, function($q) use($relation_field_name, $search_bind) {
					// 		// 	$q->whereRaw("lower($relation_field_name) like ?", [$search_bind]);
					// 		// });
					// 		// $cmd->orWhereRelation($fk, "lower($relation_field_name)", "like", "%aaa%");

					// 		// $cmd->orWhereRelationRaw("area_id", "lower(name) like ?", ["%Bali%"]);

					// 	}
					// }

				});

			}

			$totalFiltered = $cmd->count();
			if ($length > 0) {
				$cmd->skip($start)->take($length);
			}

			$cmd->latest('updated_at');
			foreach ($order as $row) {
				$cmd->orderBy($row['column'], $row['dir']);
			}
		}

		$rows = $cmd->get();
		$sql3 = Parser::getSqlWithBindings($cmd);

		if($return_cmd) {
			return $cmd;
		} else {
			$data = [
				'data' => $rows,
				// 'totalData' => $totalData,
				// 'totalFiltered' => $totalFiltered,
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				// 'draw' => $draw,
			];
			if (env('APP_DEBUG')) {
				$data['sql'] = Parser::getSqlWithBindings($cmd);
			}

			return $data;
		}
	}
}
