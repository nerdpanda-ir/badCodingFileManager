<?php session_start(); ?>
<?php require_once __DIR__.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'FileManager.php' ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="css/page/index/index.css">
</head>
<body>
<?php $fileManager = new FileManager();?>

<section class="path">
    <i class="fa-thin fa-list-tree"></i>
    <?php $segments = $fileManager->getDestinationSegments() ; ?>
    <?php $segmentUrls = $fileManager->getDestinationUrls(); ?>
    <?php foreach ($segments as $key => $segment) : ?>
        <h2>
            <?php if (isset($segmentUrls[$key])) : ?>
                <a href="<?php echo $segmentUrls[$key]?>">
                    <?php echo $segment;?>
                </a>
            <?php else: ?>
                <?php echo $segment;?>
            <?php endif;?>

                <?php if ($key!=0 and strlen($segment)>0) : ?>
                    /
                <?php endif ;?>
        </h2>
    <?php endforeach;?>

</section>
<?php if (isset($_SESSION['error'])) : ?>
    <div class="my-notify-error"><?php echo $_SESSION['error']?></div>

    <?php unset($_SESSION['error']) ?>
<?php endif;?>
<?php if (isset($_SESSION['ok'])) : ?>
    <div class="isa_success my-notify-success"><?php echo $_SESSION['ok']?></div>

    <?php unset($_SESSION['ok']) ?>
<?php endif;?>


<section class="container">
    <?php if (isset($_SESSION['content'])) : ?>
        <?php switch ($_SESSION['content']['code']) :  case 1:?>
                <?php if (count($_SESSION['content']['items']) >= 1) : ?>
                    <?php foreach ($_SESSION['content']['items'] as $item) : ?>
                        <section class="item">
                            <header>

                                <a href="?action=2&destination=<?php echo str_replace('/home/web/php/14/practice/01/media/','',$item->src)?>">
                                    <i class="fa-thin fa-trash"></i>
                                </a>
                                <?php if (!$item->type and $item->editable) : ?>
                                    <i class="fa-thin fa-edit"></i>
                                <?php endif; ?>
                            </header>
                            <main>

                                <?php echo $item->icon ;?>
                                <?php $link=$item->name;?>
                                <?php if (isset($_GET['destination'])) : ?>
                                    <?php $link = $_GET['destination'].DIRECTORY_SEPARATOR.$item->name ?>
                                <?php endif;?>
                                <a href="?destination=<?php echo $link ?>">
                                    <h4> <?php echo $item->name ;?></h4>
                                </a>
                            </main>
                            <footer>
                                size : <?php echo $item->size[1];?>
                            </footer>
                        </section>
                    <?php endforeach;?>
                <?php else : ?>
                    <section class="isa_error my-notify-error">
                        هیچ ایتمی یافت نشد !!!
                    </section>
                <?php endif;?>
            <?php break ; ?>
            <?php case 2 : ?>
                <?php $imgFormats = ['png','jpg','svg','gif']?>
                <?php $file = $_SESSION['content']['items']; ?>
                <?php if ($file->editable) : ?>
                    <form action="#" method="post">
                        <textarea name="content"><?php echo $file->content;?></textarea>
                        <button type="submit">
                            update
                        </button>
                    </form>
                <?php elseif ($file->showable and in_array($file->extension=='jpg',$imgFormats)) : ?>
                <?php echo $file->src;?>
                    <img src="http://localhost/php/14/practice/01/media/<?php echo str_replace('/home/web/php/14/practice/01/media/','',$file->src);?>" alt="">
                <?php else: ?>
                    <section class="my-notify-error isa_error">
                        این فایل قابلیت نمایش دادن ندارد !!!
                    </section>
                <?php endif; ?>
            <?php break;?>
        <?php endswitch;?>

        <?php unset($_SESSION['content']) ?>

    <?php endif;?>
</section>
</body>
</html>