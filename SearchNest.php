<!DOCTYPE html>
<head>
  <title>Spider Search Page</title>
  <meta charset='UTF-8'>
  <link rel='stylesheet' type='text/css' href='css/spider.css'>
  <?php
    include 'php/spider.php';
    $spider = new SPIDER("db-mysql", "int322_133b40", "cmXA6279", "int322_133b40");
    $g_criteria="";
    if($_POST){
      $g_criteria=$_POST['userinput'];
      $spider->SearchNest($g_criteria);
    }
  ?>
</head>
<body>
  <header>Search Spider's Nest</header>
  <aside>
    <a href='index.php'>Main Menu</a><br/>
    <a href='CrawlURL.php'>Crawl URL</a><br/>
    <a href='SearchNest.php'>Search Nest</a><br/>
  </aside>
  <article> 
  <form action='SearchNest.php' method='POST'>
    <input type='text' name='userinput' <?php if($g_criteria!=""){ echo "value='$g_criteria'"; } ?>>
    <input type='submit' value='Search Nest'>
  </form>
  <?php
    if($_POST){
      $spider->DisplayNest();
    }
  ?>
  </article>
  <div class='footerholder'>
  <footer><?php date_default_timezone_set('America/Toronto'); echo date('l F jS\, Y h:i:s A'); ?></footer>
  </div>
</body>
</html>
