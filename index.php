<!DOCTYPE html>
<head>
  <title>Spider Search Page</title>
  <meta charset='UTF-8'>
  <link rel='stylesheet' type='text/css' href='css/spider.css'>
</head>
<body>
  <header>Web Spider</header>
  <aside>
    <a href='index.php'>Main Menu</a><br/>
    <a href='CrawlURL.php'>Crawl URL</a><br/>
    <a href='SearchNest.php'>Search Nest</a><br/>
  </aside>
  <article> 
     <p>This is a very simplified version of a web spider. On the CrawlURL page a URL is entered by the user and the spider retrieves the author, description, and keywords out of the web page by extracting them from the meta tags. It then displays them to the screen and stores them in an SQL database. As it does this it also grabs the URL from the http header and stores it in the database. If it cannot find the URL from the header it defaults to the one that the user entered. If it cannot find the title, name, description, author or keywords it defaults to N/A.</p>
     <p>
       The spider is also able to search it's for the URL's that it has found. The user can enter search criteria and the spider will check it against all the URL's, names, keywords, and authors in the database and print out what it found. This is done on the search nest page. 
     </p>   
  </article>
  <div class='footerholder'>
  <footer><?php date_default_timezone_set('America/Toronto'); echo date('l F jS\, Y h:i:s A'); ?></footer>
  </div>
</body>
</html>
