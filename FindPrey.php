<!DOCTYPE html>
<head>
  <title>Spider Search Page</title>
  <meta charset='UTF-8'>
  <link rel='stylesheet' type='text/css' href='css/spider.css'>
  <?php
    include 'php/spider.php';
    $spider = new SPIDER("db-mysql", "int322_133b40", "cmXA6279", "int322_133b40");
    if($_POST){
      $spider->FindPrey($_POST['url'], $_POST['keyword']);
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
  <form action='FindPrey.php' method='POST'>
    URL:<input type='text' name='url'><br/>
    Keyword:<input type='text' name='keyword'><br/>
    <input type='submit' value='Find Prey'>
  </form>
  </article>
  <div class='footerholder'>
  <footer>This is the footer. Put the date here.</footer>
  </div>
</body>
</html>
