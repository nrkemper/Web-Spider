<?php
  //----Start of Program----
  $g_url="";
  if($_POST){
    include 'php/spider.php';
    $spider = new SPIDER("db-mysql", "int322_133b40", "cmXA6279", "int322_133b40");
    $g_url = $_POST['URL'];
    
    $valid_url = $spider->CrawlURL($g_url);
  }
?>
<!DOCTYPE html>
<head>
  <title>Web Spider</title>
  <meta charset='UTF-8'>
  <link rel='stylesheet' type='text/css' href='css/spider.css'>
</head>
<body>
  <header>Crawl a URL</header>
  <aside>
    <a href='index.php'>Main Menu</a><br/>
    <a href='CrawlURL.php'>Crawl URL</a><br/>
    <a href='SearchNest.php'>Search Nest</a><br/>
  </aside>
  <article>
  <form action='CrawlURL.php' method='POST'>
    Enter URL:<input type='text' name='URL' <?php if($g_url!=""){ echo "value='$g_url'"; }?>>
    <input type='submit' value='Crawl URL'> 
  </form>
  <?php
    if($_POST){
      if($valid_url){
        $spider->DisplayCatch();
      }
      else{
        echo "Invalid URL";
      }
    }
  ?>
  </article>
  <div class='footerholder'>
  <footer><?php date_default_timezone_set('America/Toronto'); echo date('l F jS\, Y h:i:s A'); ?></footer>
  </div>
</body>
</html>
