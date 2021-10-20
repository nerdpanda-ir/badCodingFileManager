<?php require_once 'ContentItem.php'; ?>
<?php
final class File extends ContentItem
{
    public bool $type = false;
    public string $extension;
    public bool $editable = false;
    public bool $showable=false;
    public function __construct($name, $src)
    {
        parent::__construct($name, $src);
        $this->extensionDetector();
        $this->iconDetector();
        $this->editableDetector();
        $this->sizeDetector();
        $this->showableDetector();
        if ($this->editable)
            $this->contentGetter();

    }
    private function extensionDetector()
    {
        $this->extension= pathinfo($this->name,PATHINFO_EXTENSION);
    }
    private function getAvailableIcons():array
    {
        return [
            'css'=>'<i class="fa-brands fa-css3"></i>' ,
            'img'=>'<i class="fa-thin fa-image"></i>' ,
            'html'=>'<i class="fa-brands fa-html5"></i>' ,
            'text'=>'<i class="fa-thin fa-file-lines"></i>',
            '<i class="fa-light fa-file-binary"></i>'
        ];
    }
    private function iconDetector()
    {
        $icons = $this->getAvailableIcons();
        switch ($this->extension)
        {
            default :
                $this->icon = $icons[0];
            break;
            case 'css':
                $this->icon = $icons['css'];
            break;
            case 'jpg' :
            case 'png' :
            case 'gif' :
                $this->icon = $icons['img'];
            break;
            case 'text' :
            case 'txt' :
                $this->icon = $icons['text'];
            break;
            case 'html' :
                $this->icon = $icons['html'];
            break;
        }
    }
    private function editableFormats():array
    {
        return[
            'html', 'css', 'text' , 'txt'
        ];

    }
    private function editableDetector():void
    {
        $editableFormats = $this->editableFormats();
        if (in_array($this->extension,$editableFormats))
            $this->editable = true;
    }
    protected function sizeDetector():void
    {
        $this->size[0] = filesize($this->src);
        $this->size[1] = $this->fromByteTo($this->size[0]);
    }
    private function showableFormats():array
    {
        return [
            'html','css','text','txt' , 'png' , 'jpg'
        ];
    }
    private function showableDetector()
    {
        $showableFromats = $this->showableFormats();
        if (in_array($this->extension,$showableFromats))
            $this->showable = true;
    }
    private function contentGetter()
    {
        $this->content =file_get_contents($this->src);
    }
    public function update()
    {
        file_put_contents($this->src,$_POST['content']);
        unset($_POST['content']);
    }
    public function delete():bool
    {
        return unlink($this->src);
    }
}