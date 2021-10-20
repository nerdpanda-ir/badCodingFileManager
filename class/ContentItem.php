<?php
abstract class ContentItem
{
    public string $name;
    public string $src;
    public string $icon;
    public bool $type;
    public array $size=[];
    public function __construct($name,$src)
    {
        $this->name = $name;
        $this->src = $src;
    }
    private function sizeUnits():array
    {
        return[
            'B','kB','MB','GB','TB','PB','EB','ZB','YB'
        ];
    }
    public function fromByteTo(int $byte)
    {
        $units = $this->sizeUnits();
        $counter = 0 ;
        while ($byte>1024)
        {
            $byte/= 1024 ;
            $counter++;
        }
        $result = number_format($byte,2).' '.$units[$counter];
        return $result;
    }
    protected abstract function sizeDetector():void ;
}