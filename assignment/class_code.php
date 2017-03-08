<html>
	<head>
		<meta charset="utf-8">
		<title>WMDB Search></title>
		<link rel='stylesheet' type='text/css' href='css/wmdb-search.css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" 
		integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	</head>
	<style>
		.row {
			margin-top: 10px;
		}
	</style>
	<body>

	<div class='container'>
		<form action='#' method='get' class='row' autocomplete='on'>
		   <select class="form-control offset-sm-2 col-sm-1" name='type'>
		      <option value='name'>name</option>
		      <option value='title'>movie title</option>
		      <option value='both'>both</option>
		    </select>
		    <input type="text" class="form-control col-sm-6" name='sought'>
		    <button type="submit" class="btn btn-primary col-sm-1">Submit</button>
		</form>
	</div>

	<?php

	require_once("/home/cs304/public_html/php/DB-functions.php");

	// The following defines the data source name (username, password,
	// host and database).

	require_once('mngo_mhejmadi_dsn.inc');

	// The following connects to the database, returning a database handle (dbh)

	$dbh = db_connect($mngo_mhejmadi_dsn);

	// Here's our query; a simple string with a placeholder

	$sql_name = "SELECT distinct name,birthdate from person  
				 where person.name like concat('%',?,'%');";

	$sql_name_movies = "SELECT distinct title from person,credit,movie
						where person.name like concat('%',?,'%') and person.nm=credit.nm and credit.tt=movie.tt;";

	$sql_name_count = "SELECT distinct count(*) from person  
				 where person.name like concat('%',?,'%');";

	$sql_title = "SELECT distinct title,`release`,name from
				  movie,person
				  where movie.title like concat('%',?,'%') 
				  	and movie.director=person.nm;";

	$sql_title_count = "SELECT distinct count(*) from
				  movie,person as director
				  where movie.title like concat('%',?,'%') 
				  	and movie.director=director.nm;";

	$sql_title_actors = "SELECT distinct name from
			  movie,credit,person
			  where movie.title like concat('%',?,'%')
			  	and movie.tt=credit.tt
			  	and person.nm=credit.nm;";

	// This executes the query. We supply the DBH and the query string. It
	// gives us back a resultset object.

	$type = $_REQUEST['type'];
	$request = $_REQUEST['sought'];

	echo "<p>Here are entries from ${_REQUEST['sought']}:\n";
	echo "<ol>\n";


	#------------------ FUNCTIONS -----------------
	function write_name_several($resultset) {
		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "<li><a href='#'>${row['name']}  ${row['birthdate']}</a>";
		}
	}

	function write_name_single($resultset) {
		global $dbh, $sql_name_movies;
 		$movies = prepared_query($dbh,$sql_name_movies,array($_REQUEST['sought']));
 		//make fancier later
 		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	    	echo "<h3>${row['name']} \n was born on ${row['birthdate']}</h3>";
	    }

	    echo "<ul>Filmography: ";
	    while($row = $movies->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	    	echo "<li>${row['title']}";
	    }	
	}

	function write_title_several($resultset) {
		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "\t<li><a href='#'>${row['title']}  (${row['release']})</a>\n";
		 }
	}

	function write_title_single($resultset) {
		global $dbh, $sql_title_actors;
		$actors = prepared_query($dbh,$sql_title_actors,array($_REQUEST['sought']));

		while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    echo "<h3>${row['title']}  (${row['release']}) </h3>";
		    echo "<h6>directed by: ${row['name']}</h6>";
		}

		echo "<ul>Cast: ";
	    while($row = $actors->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	    	echo "<li>${row['name']}";
	    }
	}

	if ($type == 'name') {
		$resultset = prepared_query($dbh,$sql_name,array($_REQUEST['sought']));

		// FIGURE OUT if there's just one result or several
		$counter = prepared_query($dbh, $sql_name_count, array($_REQUEST['sought']));
		$count = 0;
		while ($row = $counter->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$count = $row['count(*)'];
		}

		if ($count > 1) {
			write_name_several($resultset);
	 	} else if ($count == 1) {
	 		write_name_single($resultset);
	 	} else {
	 		echo "Sorry, no names match $request";
	 	}
			
	} else if ($type == 'title') {

		// COUNT NUMBER OF ROWS IN RESULT SET
		$counter = prepared_query($dbh, $sql_title_count, array($_REQUEST['sought']));
		$count = 0;
		while ($row = $counter->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$count = $row['count(*)'];
		}

		$resultset = prepared_query($dbh,$sql_title,array($_REQUEST['sought']));

		if ($count > 1) {
			write_title_several($resultset);
		} else if ($count == 1) {
			write_title_single($resultset);
		} else {
			echo "Sorry, no movies match $request";
		}

	} else if ($type == 'both') {
		$name_resultset = prepared_query($dbh,$sql_name,array($_REQUEST['sought']));
		$title_resultset = prepared_query($dbh,$sql_title,array($_REQUEST['sought']));

		$name_count = prepared_query($dbh,$sql_name_count,array($_REQUEST['sought']));
		$title_count = prepared_query($dbh,$sql_title_count,array($_REQUEST['sought']));

		if ($name_count > 0) {
			echo "<h3>$name_count Names matched</h3>";
			if ($name_count > 1) {
				write_name_several($name_resultset);
			} else {
				write_name_single($name_resultset);
			}
		}

		if ($title_count > 0) {
			echo "<h3>$title_count Movies matched</h3>";
			if ($title_count > 1) {
				write_title_several($title_resultset);
			} else {
				write_title_single($title_resultset);
			}
		}

		if (($title_count == 0) && ($name_count == 0)) {
			$request = $_REQUEST['sought'];
			echo "<h3> No Results for $request</h3>";
		}

	}
	// The resultset object has a fetchRow method that can give us back the
	// next row of the resultset, stored in an associative array with
	// keys named for the columns we asked for (nm,name). 

	?>
	</ol>

	</body>
	<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" 
	integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" 
	crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" 
	integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" 
	crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" 
	integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" 
	crossorigin="anonymous"></script>
</html>