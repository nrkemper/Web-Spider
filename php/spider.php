<?php
    class SPIDER{
      private $_cnx;
      private $_queryresult;
      private $_keywordsresult;
      private $_url;
      private $_host;
      private $_uri;
      private $_domain;
      private $_name;
      private $_date;
      private $_author;
      private $_keywords;
      private $_descript;
      private $_crawledurl;//indicates whether the spider has crawled a website
      private $_criteria;  //stores the criteria that the user entered for his/her search

      public function __construct($server, $username, $password, $database){
        $this->_queryresult = "";                           //initialize the variables
        $this->_host = 'N/A';
        $this->_uri = 'N/A';
        $this->_url = "";
        $this->_domain = "N/A";
        $this->_name = "N/A";
        $this->_date = "";
        $this->_author = 'N/A';
        $this->_keywords = "NONE";
        $this->_descript = "NONE";
        $this->_crawledurl=0;
        $this->_criteria = "";

        $this->_cnx = mysql_connect($server, $username, $password) or die("Could not connect to database");//establishes the connection
        mysql_select_db($database, $this->_cnx);
      }


      private function KeywordExists($url, $keyword){             
        /* Ensures that the keyword doesn't exist before entering it into the table to avoid violating
        the unique constraint on the keyword*/
        
        $exists = 0;
        $query = "SELECT 1 FROM Keywords WHERE URLNo IN(SELECT URLNo FROM URLS WHERE URL='$url') AND keyword='$keyword'";

        $result = mysql_query($query, $this->_cnx) or die(mysql_error() . "the query was: $query");
        if(mysql_num_rows($result)>0){
          $exists=1;
        }
 
        return $exists;
      }

      private function AddKeyword($url, $keyword){
        //add a keyword to the database 

        $success=1;
        
        $keyword = preg_replace("/'/", "\'", $keyword); 
  
        $query = "INSERT INTO Keywords(URLNo, Keyword) VALUES((SELECT URLNo FROM URLS WHERE URL='$url'),'$keyword')";
        
        mysql_query($query, $this->_cnx) or die(mysql_error() . " the query was: $query");
    
        return $success;
      }

      private function URLExists($url){
        /* Ensures that the url doesn't exist before entering it into the table to avoid violating
        the unique constraint on the keyword*/

        $exists = 0;
        $query = "SELECT 1 FROM URLS WHERE URL='$url'";

        $result = mysql_query($query, $this->_cnx) or die(mysql_error() . " the query was: $query");
        if(mysql_num_rows($result)>0){
          $exists = 1;
        }
 
        return $exists;
      }
   
      private function UpdateURL($url, $author, $name, $descript, $storedate){
        /* If the URL exists just update the url*/

        $query = "UPDATE URLS SET Author='$author', Name='$name', Description='$descript', StoreDate='$storedate' WHERE URL='$url'";

        mysql_query($query, $this->_cnx) or die(mysql_error() . " the query was: $query");
      }
 
      private function AddURL($url, $author, $name, $descript, $storedate){
        $success=1;
        

        $url       = preg_replace("/'/", "\'", $url);       //validates for any character's that would cause 
        $author    = preg_replace("/'/", "\'", $author);    // the insert statement to crash like an apostrophe
        $name      = preg_replace("/'/", "\'", $name); 
        $descript  = preg_replace("/'/", "\'", $descript); 
        $storedate = preg_replace("/'/", "\'", $storedate); 
  
        $query = "INSERT INTO URLS(URL, Author, Name, Description, StoreDate) VALUES('$url', '$author', '$name', '$descript', '$storedate')";
        
        mysql_query($query, $this->_cnx) or die(mysql_error() . " the query was: $query");
 
        return $success;
      }

      private function StoreMetaTags(){
        if(!$this->URLExists($this->_url)){
          $this->AddURL($this->_url, $this->_author, $this->_name, $this->_descript, $this->_date);
        } 
        else{
          $this->UpdateURL($this->_url, $this->_author, $this->_name, $this->_descript, $this->_date);
        }

        if(sizeof($this->_keywords)>1){
          for($i=0;$i<sizeof($this->_keywords);$i++){
            if(!$this->KeywordExists($this->_url, $this->_keywords[$i])){
             $this-> Addkeyword($this->_url, $this->_keywords[$i]);
            }
          }
        } 
      }
     
      private function ValidateURL($url){    //validates the url that the user entered
        $valid = 1;                          //to ensure that it is compliant with the 
                                             //get_headers function
        $format = "/^https?:\/\/.*\.(ca|com|org|net)/"; 
        
        if(!preg_match($format, $url)){
          $valid=0;
        }
       
        return $valid;
      }

      private function SecureURL($url){
        //secures the url that the user entered and returns the secured URL
        $invalid_chars = "/[^a-zA-Z0-9\-_.~!*'();:@&=$,\/?%#\[\]]/";
        
        $url = preg_replace($invalid_chars, "", "$url");
        
        return $url;
      }

      private function GetMetaTags($url){
        $result = `wget -O - -q $url`;  //downloads the URL that the user entered with the wget command

        if(preg_match("/<title>(.*)<\/title>/", $result, $matches)){//searches for the title with a regular expression
          $this->_name = $matches[1]; 
        }
        else{
          $this->_name = "N/A";
        }


        $format = "/<meta *name *= *[\"']([^'\"]*)[\"'] *content *= *['\"]([^'\"]*)['\"] *\/?\>/";
        preg_match_all($format, $result, $meta_tag_matches);//gets all the meta tags
        
        /*
          meta_tag_matches[X][0]->> full matches(will be blank)
          meta_tag_matches[X][1]->> names of meta tags
          meta_tag_matches[X}[2]->> content of meta tags
  
        */
        

        for($i=0;$i<sizeof($meta_tag_matches[1]);$i++){//cycles throught all the meta tags
          if($meta_tag_matches[1][$i]=="author"){//if the name=author
            $this->_author = $meta_tag_matches[2][$i];
          }
          else if($meta_tag_matches[1][$i]=="keywords"){//if the name=keywords
            $this->_keywords = explode(",",$meta_tag_matches[2][$i]); 
          }
          else if($meta_tag_matches[1][$i]=="description"){//if the name=description
            $this->_descript = $meta_tag_matches[2][$i];
          }
        } 
      }

      private function GetHeaders($url){
        $headers = get_headers($url);//gets the headers 

        for($i=0;$i<sizeof($headers);$i++){//cycles through the array of all the headers
          if(preg_match("/^Location:(.*)$/", $headers[$i], $loc_matches)){//if the headers[i]=location
            $this->_url = $loc_matches[1];
          }
 
          if(preg_match("/^Host:(.*)$/", $headers[$i], $host_matches)){//if the header included the Host element
            $this->_host = $host_matches[1]; //save it
          }   

          if(preg_match("/^Requested URI:(.*)$/", $headers[$i], $uri_matches)){//if the headers included the Requested URI element
            $this->_uri = $uri_matches[1]; 
          }   
          
          if(preg_match("/^Domain:(.*)$/", $headers[$i], $domain_matches)){//if the header included the Domain
            $this->_domain = $domain_matches[1]; 
          }

          if(preg_match("/^Date:(.*)$/", $headers[$i], $date_matches)){//if the header included the Date
            $this->_date = $date_matches[1]; //need backreferencing
          }     
        }

        if($this->_url == "N/A" && $this->_host != "N/A" && $this->_uri != "N/A"){ //if the URL wasn't found through the Location
          $this->_url = $this->_host . $this->_uri;                                // header use the host and uri headers
        }

        if($this->_url == "N/A" && $this->_domain != "N/A"){  //if the URL still wasn't found use the Domain header
          $this->_url = $this->_domain;
        }

        if($this->_url == "N/A"){  // if the URL still wasn't found just use the URL that the user entered
          $this->_url = $url;
        }
      }

      public function FindPrey($url, $keyword){//doesn't work. Ignore
        $success=1;

        $url=$this->SecureURL($url);      //broken

        if($this->ValidateURL($url)){
          $wget = `wget -O - -q $url`;

          $wget = preg_replace("/\"/", "\\\"", $wget);
          $wget = preg_replace("/;/", "\\;", $wget);
          $wget = preg_replace("/<script>/", "\<script\>", $wget);
          $wget = preg_replace("/<\/script>/", "\<\\\/script\>", $wget);

          echo "<script>\n";
          echo "var newWindow= window.open();\n";
          echo "newWindow.document.write(\"$wget\");\n";
          echo "newWindow.document.close();\n";
          echo "</script>\n";
        }
        else{
          $success=1;
        }
  
        return $success;
      }

      public function CrawlURL($url){//the public function that handles all the tasks 
        $success=1;                  //to crawl a URL
        $url=$this->SecureURL($url);//secures the url
        
        if($this->ValidateURL($url)){//validates it for the correct syntax
          $this->GetHeaders($url);   //gets the headers
          $this->GetMetaTags($url);  //gets the meta tags and the title
          $this->StoreMetaTags();    //stores the data
          $this->_crawledurl=1;     //sets the flag to indicate that this spider has crawled a url
        }
        else{
          $success=0;
        }
        return $success;//returns if it the spider successfully crawled the url
      }

      private function SecureUserInput($criteria){
        $invalid_chars = "/[^a-zA-Z0-9\-_.~!();:@&=$,\/?%#\[\]]/";        
        $criteria = preg_replace($invalid_chars, "", $criteria);
 
        return $criteria;
      }

      public function SearchNest($criteria){
        $criteria = $this->SecureUserInput($criteria);//secures the search criteria
        $this->_criteria = $criteria;

        $query  = "SELECT * ";
        $query .= "FROM URLS ";
        $query .= "WHERE URLNo IN(SELECT URLNo ";
        $query .=                "FROM Keywords ";
        $query .=                "WHERE Keyword='$criteria') ";
        $query .= "OR URL='$criteria' ";
        $query .= "OR Name='$criteria' ";
        $query .= "OR Author='$criteria';";

        $this->_queryresult = mysql_query($query, $this->_cnx) or die(mysql_error() . " the query was: $query"); //searches the database and returns 
                                                                                                                 //where the criteria matches the author, name, one of the keywords or the url  
     } 

      public function DisplayCatch(){
        //displays the info that the spider has stored
        if($this->_crawledurl){
          echo "Name: $this->_name<br/>\n";
          echo "URL: $this->_url<br/>\n";
          echo "Host: $this->_host<br/>\n";
          echo "Domain: $this->_domain<br/>\n";
          echo "Requested URI: $this->_uri<br/>";
          echo "Date: $this->_date<br/>\n";
          echo "Author: $this->_author<br/>\n";
        
          if(sizeof($this->_keywords)>1){
            echo "Keywords<br/>\n";
      
            for($i=0;$i<sizeof($this->_keywords);$i++){
              echo "&nbsp;&nbsp;[$i] " . $this->_keywords[$i] . "<br/>\n";
            }
          }
          else{
            echo "Keywords: NONE<br/>\n";
          }
          echo "Description: $this->_descript<br/>\n";
        }
        else{
          echo "The spider does not have a website in it's web<br/>\n";
        }
      }
      
      public function DisplayNest(){
        echo "<table>\n";//displays whats in the nest based on the criteria that the user entered
        echo "<tr>\n";
        echo "<th>URLNo</th>\n";//displays the column headings
        echo "<th>URL</th>\n";
        echo "<th>Name</th>\n";
        echo "<th>Author</th>\n";
        echo "<th>Description</th>\n";
        echo "<th>Store Date</th>\n";
        echo "<th>Keywords</th>\n";
        echo "</tr>\n";
 
       while($row = mysql_fetch_assoc($this->_queryresult)){
          echo "<tr>\n";
          echo "<td>" . $row['URLNo'] . "</td>\n";
          
          echo "<td>";
          if(strtoupper($row['URL']) != strtoupper($this->_criteria)){//if the URL in the row matches the criteria that the user entered
            echo $row['URL'];
          }
          else{
            echo "<span class='highlight'>" . $row['URL'] . "</span>"; //highlight it
          } 
          echo "</td>\n";
          
          echo "<td>";
          if(strtoupper($row['Name']) != strtoupper($this->_criteria)){//if the Name in the row matches the criteria  
            echo $row['Name']; 
          }
          else{
            echo "<span class='highlight'>" . $row['Name'] . "</span>";//highlight it
          }
          echo "</td>\n";
          
          echo "<td>";
          if( strtoupper($row['Author']) != strtoupper($this->_criteria)){//if the author matches the criteria highlight it 
            echo $row['Author'];
          }
          else{
            echo "<span class='highlight'>" . $row['Author'] . "</span>, ";
          }
          echo "</td>\n";
          echo "<td>" . $row['Description'] . "</td>\n";
          echo "<td>" . $row['StoreDate'] . "</td>\n";
          echo "<td>";
          $result = mysql_query("SELECT Keyword FROM Keywords WHERE URLNo='". $row['URLNo'] . "'", $this->_cnx);
          while($row2= mysql_fetch_assoc($result)){
            if(strtoupper($row2['Keyword']) != strtoupper($this->_criteria)){//displays all the keywords from that URL
              echo $row2['Keyword'] . ", ";
            }
            else{
              echo "<span class='highlight'>" . $row2['Keyword'] . "</span>, ";//and highlight them if they match the search criteria
            }
          }
          echo "</td>\n";
          echo "</tr>\n";
        }
        echo "</table>\n"; 
      }

      function __destruct(){
        mysql_close($this->_cnx);
      }
    }
?>
