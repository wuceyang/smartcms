<?php
/**
 * 组权限
 */
	namespace App\M;

	class GroupPrivilege extends Model{

		public static $table = 'group_privileges';

		/**
		 * 添加分组以及对应的权限
		 * @param int   $groupId 分组ID
		 * @param mixed $menuId  分组拥有权限的菜单id
		 * @return int
		 */
		public function setGroupPrivilege($groupId, $menuId){

			$menuId  = is_array($menuId) ? array_map('intval', $menuId) : intval($menuId);

			$menuIds = is_array($menuId) ? implode(',', $menuId) : $menuId;

			$data = [
					'menu_id' => $menuIds,
					'group_id' => intval($groupId),
					];

			return self::insert($data);
		}

		/**
		 * 更新分组的菜单权限
		 * @param  int 	 $id     权限记录的id
		 * @param  mixed $menuId 分组拥有权限的菜单id
		 * @return int
		 */
		public function updateGroupPrivilege($id, $menuId){

			$menuId  = is_array($menuId) ? array_map('intval', $menuId) : intval($menuId);

			$menuIds = is_array($menuId) ? implode(',', $menuId) : $menuId;

			return self::where('id = ?', [intval($id)])->update(['menu_id' => $menuIds]);
		}

		/**
		 * 根据分组ID查询各自的权限
		 * @param  mixed $groupId 分组ID(单个id或者数组)
		 * @return array
		 */
		public function getGroupPrivileges($groupId){

			$groupIds   = is_array($groupId) ? $groupId : [$groupId];

			$groupIds   = array_map('intval', $groupIds);

			$privileges = self::where('group_id IN (' . implode(',', $groupIds) . ')')->getRows();

			if(!$privileges) return [];

			$retRows = [];

			foreach ($privileges as $k => $v) {

				$retRows[$v['group_id']] = $v;
			}

			return is_array($groupId) ? $retRows : $retRows[$groupId];
		}
	}
