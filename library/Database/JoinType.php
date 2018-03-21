<?php
	namespace Library\Database;

	class JoinType{
		const INNER = 'INNER JOIN';
        const LEFT  = 'LEFT JOIN';
        const RIGHT = 'RIGHT JOIN';
        const CROSS = 'CROSS JOIN';
        const OUTER = 'OUTER JOIN';
	}