<?php 

class SponsorGroup extends SimpleAdminModulBase
{
	function SaveItem($id, $row)
	{
		SqlManager::GetInstance ()->Update ( $this->table, array ('name' => $row ['name']), 'id=\'' . $id . '\'' );
	}
	
	function AddNewItem($data)
	{
		SqlManager::GetInstance ()->Insert ( $this->table, array ('name' => $data ['name']) );
	}
}