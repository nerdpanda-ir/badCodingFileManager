<?php require_once 'File.php' ;?>
<?php require_once 'Dir.php' ;?>
<?php
final class FileManager
{
    private string $baseDir;
    private string $action;
    private string $destination;
    private bool $destinationIsExist=false;
    private bool  $destinationType;
    private array $destinationSegments;
    private array $segmentUrls;
    public function __construct()
    {
        $this->init();
        $this->fetchFiles();
    }
    private function init()
    {
        $this->initializeBaseDir();
        $this->initializeDestination();
        $this->initializeAction();
        $this->initializeDestinationIsExist();
        $this->initializeDestinationType();
        $this->initializeDestinationSegments();
        $this->initializeDestinationSegmentUrls();
    }
    private function initializeBaseDir():void
    {
        $this->baseDir= dirname(__DIR__).DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR;
    }
    private function initializeDestination():void
    {
        $this->destination=$this->baseDir;
        if (isset($_GET['destination']) and strlen($_GET['destination'])>=1)
            $this->destination.=$_GET['destination'];
    }
    private function initializeDestinationIsExist()
    {

        if (file_exists($this->destination))
            $this->destinationIsExist = true;
    }
    private function isFile(string $src):bool
    {
        if (filetype($src)=='file')
            return true;
        return false;
    }
    private function isDir(string $src):bool
    {
        if (filetype($src)=='dir')
            return true;
        return false;
    }
    private function destinationIsFile():bool
    {
        return $this->isFile($this->destination);
    }
    private function destinationIsDir():bool
    {
        return $this->isDir($this->destination);
    }
    private function setDestinationTypeToTrue()
    {
        $this->destinationType = true;
        $this->destination.= DIRECTORY_SEPARATOR;
    }
    private function initializeDestinationType():void
    {
        if ($this->destinationIsExist and $this->destinationIsFile())
            $this->destinationType= false;
        else
            if ($this->destinationIsExist and $this->destinationIsDir())
                $this->setDestinationTypeToTrue();
            else
                if(!$this->destinationIsExist)
                    $_SESSION['error'] = 'چنین فایل یا دایرکتوری   وجود ندارد !!!';
    }
    private function removeBasePathFromDestination():string
    {
        $destination = $this->destination;
        $base = $this->baseDir;
        $destination = str_replace($base,'',$destination);
        return $destination;
    }
    private function clearDestinationSlash($destination):string
    {
        $destinationCount = strlen($destination)-1;
        $lastCh =substr($destination,$destinationCount);
        if ($lastCh==DIRECTORY_SEPARATOR)
            $destination = substr($destination,0,$destinationCount);
        return $destination;
    }
    private function initializeDestinationSegments()
    {
        $destination = $this->removeBasePathFromDestination();
        $destination = $this->clearDestinationSlash($destination);
        $this->destinationSegments[] = '/';
        $this->destinationSegments = array_merge($this->destinationSegments,explode('/',$destination));

    }
    private function initializeDestinationSegmentUrls()
    {
        $segments = $this->destinationSegments;
        array_pop($segments);
        $this->segmentUrls = [] ;
        foreach ($segments as $key => $segment)
        {
            $slice = array_slice($segments,0,$key+1);
            $this->segmentUrls[] = '?destination='.implode('/',$slice);
            $this->segmentUrls = preg_replace('/\/\//','/',$this->segmentUrls);
        }
    }
    public function getDestinationSegments():array
    {
        return $this->destinationSegments;
    }
    public function getDestinationUrls():array
    {
        return $this->segmentUrls;
    }
    private function setAction(string $action)
    {
        $okActions = ['1','2','3'];
        if (in_array($action,$okActions))
            $this->action=$action;
        else
            $_SESSION['error']='action نامتعتبر است !!!';
    }
    private function initializeAction():void
    {
        if (isset($_GET['action']) and strlen($_GET['action'])>0)
            $this->setAction($_GET['action']);
        else
            $this->action='1';
    }
    private function getDirContentList(array &$contents)
    {
        unset($contents[0]);
        unset($contents[1]);
        $contents= array_values($contents);

    }
    private function dirContentDetector($item)
    {
        $src = $this->destination.$item;
        if ($this->isFile($src))
            return new File($item,$src);
        else
            if ($this->isDir($src))
                return new Dir($item,$src);
    }
    private function getDirContent():array
    {
        $contents = scandir($this->destination);
        $this->getDirContentList($contents);
        $contents = array_map([$this,'dirContentDetector'],$contents);
        return $contents;
    }
    private function setDirectoryContentsToResult()
    {
        $contents = $this->getDirContent();
        $_SESSION['content']['code']=1;
        $_SESSION['content']['items']=$contents;
    }
    private function doDeleteDirectory()
    {
        $dir = new Dir(pathinfo($this->destination,PATHINFO_BASENAME),$this->destination);
        $dir->delete();
    }
    private function dirActionDetect():void
    {
        switch ($this->action)
        {
            case '1':
                $this->setDirectoryContentsToResult();
            break;
            case 2 :
                $this->doDeleteDirectory();
            break;

        }
    }
    private function fileActionDetect()
    {
        $file = new File(pathinfo($this->destination,PATHINFO_BASENAME),$this->destination);
        if (isset($_POST['content']))
        {
            $file->update();
            $file = new File(pathinfo($this->destination,PATHINFO_BASENAME),$this->destination);
            $_SESSION['ok']='فایل با موفقیت اپدیت شد !!!';
        }
        switch ($this->action)
        {
            case 1 :
                $_SESSION['content']['items'] = $file;
                $_SESSION['content']['code'] = '2';
            break;
            case 2 :
                if ($file->delete())
                    $_SESSION['ok']='فایل با موفقیت حذف شد !!';
            break;
        }
    }
    private function fetchFiles()
    {
        if ($this->destinationIsExist and $this->destinationType)
            $this->dirActionDetect();
        if ($this->destinationIsExist and !$this->destinationType)
            $this->fileActionDetect();
    }

}