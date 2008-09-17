<?php

function plugin__sitemap($params, &$q = null)
{
	$params['level']      = isset($params['level'])      ? $params['level'] : 0;
	if ($params['table'] && isset($params['uri'][$params['level']])) {
				
		if ($q = $params['db']->queryRow("
			SELECT *
			FROM $params[table]
			WHERE active = '1'
				AND parent_id = '$params[parent_id]'
				AND slug IN ('".$params['uri'][$params['level']]."','*')
		")) {
			if ($q[$params['slug']] == '*') return $q;
			else {
				$params['parent_id'] = $q['id'];
				$params['level']     = $params['level']+1;
				return plugin__sitemap($params, $q);
			}
			
		}
	} else {		
		return $q;
	}
}