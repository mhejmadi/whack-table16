<html>
<head>
<title>Exercise: Listing Addresses</title>
</head>
<body>

<P> The following is the result of 

<!-- <pre>
SELECT name,city,state
FROM contact inner join address on (id)
WHERE state = ?
</pre> -->

<form action='#' method='get'>
	<select name="type">
	  <option value="name">name</option>
	  <option value="title">title</option>
	  <option value="both">both</option>
	</select>
	<br>
  	<input type="text" name="sought" value="">
  	<br>
  	<input type="submit" value="Submit">
</form> 

<p>

<?php

require_once("/home/cs304/public_html/php/DB-functions.php");

// The following defines the data source name (username, password,
// host and database).

require_once('mngo_mhejmadi_dsn.inc');

// The following connects to the database, returning a database handle (dbh)

$dbh = db_connect($mngo_mhejmadi_dsn);

// Here's our query; a simple string with a placeholder

$sql_name = "SELECT name,birthdate,title from name,credit,movie  
			 where name.name like ? and name.nm=credit.nm and credit.tt=movie.tt;";

$sql_title = "SELECT distinct title,`release`,director.name as dname,actors.name as aname from
			  movie,credit, name as director, name as actors
			  where movie.title like ? 
			  	and movie.director=director.nm
			  	and movie.tt=credit.tt
			  	and actors.nm=credit.nm;";

// This executes the query. We supply the DBH and the query string. It
// gives us back a resultset object.

$type = $_REQUEST['type'];

echo "<p>Here are entries from ${_REQUEST['sought']}:\n";
echo "<ol>\n";

if ($type == 'name') {
	$resultset = prepared_query($dbh,$sql_name,array('%'.$_REQUEST['sought'].'%'));

	// COUNT NUMBER OF ROWS IN RESULT SET
	$counter = 0;
	while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		if ($counter > 1) {
			break;
		}
		$counter = $counter + 1;
	}

	if ($counter > 1) {
		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "<li>${row['name']}  ${row['birthdate']} <a href='#'>More</a>";
		}
 	} else {
 		//make fancier later
 		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	    	echo "<li>${row['name']}  \t\tbirthdate: ${row['birthdate']}  \t\ttitle: ${row['title']} \n";
	    }
 	}
		
} else if ($type == 'title') {

	// COUNT NUMBER OF ROWS IN RESULT SET
	$counter = 0;
	while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		if ($counter > 1) {
			break;
		}
		$counter = $counter + 1;
	}

	$resultset = prepared_query($dbh,$sql_title,array('%'.$_REQUEST['sought'].'%'));
	if ($counter > 1) {
		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "<li>${row['title']}  ${row['release']}  <a href='#'>More</a>\n";
		 }
	} else {
		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "<li>${row['title']}  ${row['release']}  ${row['dname']} ${row['aname']} \n";
		}
	}
} else {

}
// The resultset object has a fetchRow method that can give us back the
// next row of the resultset, stored in an associative array with
// keys named for the columns we asked for (nm,name). 

?>
</ol>

</body>
</html>