Web-Spider
==========

This is a simple Web Spider written in PHP. The user can enter a URL of a webpage and the spider will extract all the meta data from it and store it into a MySQL database. The database will contain the URL of the webpage, any keys that were extracted, the author of the webpage a the description of the webpage that was provided via the meta data. The user can then search through their "nest"(datbase) via a phrase and view any contents that match the phrase they provided. If no phrase was entered the spider will display the entire contents of it's nest. Any URL's that are entered by the user are validated with regular expressions and all entries that go into the database are secured for SQL injection attacks before they are inputted.
