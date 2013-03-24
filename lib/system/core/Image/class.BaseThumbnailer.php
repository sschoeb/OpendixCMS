<?php

abstract class BaseThumbnailer
{	
	protected $outputWidth = 100;
	protected $outputHeight = 100;
	protected $prefix = '';
	
	public function SetOutputSize($width, $height)
	{
		$this -> outputWidth = $width;
		$this -> outputHeight = $height;
	}
	

	public function SetThumbPrefix($prefix)
	{
		$this -> prefix = $prefix;
	}
	
	
	protected function CalcOutputSize()
	{
		
		if ($this->pictureInfo [1] <= $this->maxHigh && $this->pictureInfo [0] <= $this->maxWidth && $this->onlySmaller)
		{
			$this->neueBreite = $this->pictureInfo [0];
			$this->neueHoehe = $this->pictureInfo [1];
			return false;
		}
		
		//Neue Werte bestimmen
		if ($this->pictureInfo [0] > $this->pictureInfo [1])
		{
			$this->neueBreite = $this->maxWidth;
			$this->neueHoehe = intval ( $this->pictureInfo [1] * $this->neueBreite / $this->pictureInfo [0] );
		} else
		{
			$this->neueHoehe = $this->maxHigh;
			$this->neueBreite = @intval ( $this->pictureInfo [0] * $this->neueHoehe / $this->pictureInfo [1] );
		}
		return true;
	}
}
