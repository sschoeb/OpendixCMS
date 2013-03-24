<?php

class AboAdmin extends SimpleAdminModulBase
{
	function SaveItem($id, $row)
	{
		SqlManager::GetInstance ()->Update ( $this->table, array ('name' => $row ['name'], 'Preis' => $row ['preis'], 'Description' => $row ['desc'], 'folge' => $row['folge']), 'id=\'' . $id . '\'' );
	}
	
	function AddNewItem($data)
	{
		SqlManager::GetInstance ()->Insert ( $this->table, array ('name' => $data ['name'], 'Preis' => $data ['preis'], 'Description' => $data ['desc']) );
	}
	
	protected function getOrderColumn()
	{
		return "folge";
	}
}