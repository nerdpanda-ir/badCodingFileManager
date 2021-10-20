<?php require_once 'ContentItem.php'; ?>
<?php require_once 'File.php'; ?>
<?php
final class Dir extends ContentItem
{
    public bool $type = true;
    public array $size =[];
    public function __construct($name, $src)
    {
        parent::__construct($name,$src);
        $this->icon = '<i class="fa-thin fa-folder"></i>';
        $this->sizeDetector();
    }
    public function delete()
    {
        $innerItems = scandir($this->src);
        array_shift($innerItems);
        array_shift($innerItems);

        foreach ($innerItems as $item)
        {
            if(filetype($this->src.$item)=='file')
            {
                $file = new File($item,$this->src.$item);
                $file->delete();
            }
            else if (filetype($this->src.$item)=='dir')
            {
                $dir = new self($item,$this->src.$item.DIRECTORY_SEPARATOR);
                $dir->delete();
            }
        }

        rmdir($this->src);
    }
    protected function sizeDetector(): void
    {
        $total = 0 ;
        $contents = scandir($this->src);
        array_shift($contents);
        array_shift($contents);
        foreach ($contents as $content)
        {
            $fullSrc = $this->src.DIRECTORY_SEPARATOR.$content;
            $type =filetype($fullSrc);
            if ($type=='file')
            {
                $file = new File($content , $fullSrc);
                $total+=$file->size[0];
            }
            else
                if ($type=='dir')
                {
                    $dir = new self($content,$fullSrc);
                    $total+=$dir->size[0];
                }
        }
        $this->size[]=$total;
        $this->size[]=$this->fromByteTo($total);
    }
}
