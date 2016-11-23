<?php
	namespace App\M;

	class UserGroup extends Model{

		public static $table = 'user_group';

		/**
		 * 创建分组
		 * @param  string $groupName 分组名称
		 * @param  int    $creatorId 创建分组的用户ID
		 * @return int 分组ID
		 */
		public function createGroup($groupName, $creatorId){

			$data = [
					'group_name'      => trim($groupName),
					'create_by'       => intval($creatorId),
					'create_dateline' => TIME,
					];

			return self::insert($data);
		}

		/**
		 * 取全部分组
		 * @return array 分组记录
		 */
		public function getAllGroups(){

			return self::orderBy(['id ASC'])->getRows();
		}

		/**
		 * 根据分组id取分组信息
		 * @param  mixed $groupId 分组id
		 * @return array
		 */
		public function getGroupsById($groupId){

			return $this->getInfoById($groupId);
		}

		/**
		 * 更新分组信息
		 * @param int 	$groupId 	分组ID
		 * @param array $updateInfo 需要更新的信息
		 * @return int
		 */
		public function updateGroupInfo($groupId, $updateInfo){

			return self::where('id = ?', [intval($groupId)])->update($updateInfo);
		}
	}
