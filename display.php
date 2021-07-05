#!/usr/local/bin/php
<!DOCTYPE html>
<html lang="en">

<?php
if (isset($_POST['show']))
{ // they did submit
    $person = $_POST['person']; //gets the name of the user
    try
    { // attempt to establish connection
        $mydb = new SQLite3('images.db'); // opens or creates the database
    }
    catch(Exception $ex)
    { // may throw
        echo $ex->getMessage();
    }    
    //creates the table if it doesn't already exist in order to avoid error 
    $statement = 'CREATE TABLE IF NOT EXISTS info(name TEXT, title TEXT, file_name TEXT, views INTEGER, time_uploaded TEXT);';
    $run = $mydb->query($statement);
    //this statement counts the number of rows with images by the specified user 
    $stmt = $mydb->prepare("SELECT COUNT(1) FROM info  WHERE name = :name;");
    $stmt->bindValue(':name', $person, SQLITE3_TEXT);
    $result = $stmt->execute();
    //creates the page title
    echo "<head>";
    $page_title = $person . "'s photos";
    echo "<title> $page_title </title>";
    echo "<meta charset='UTF-8'>";
    echo "</head>";

    if ($result)
      // so no errors in the query
    {
        $row = $result->fetchArray();
        if ($row[0] >= 1) //so there are entries for specified person
        {   
            //selects the images uploaded by the person
            $stmt = $mydb->prepare("SELECT name, title, file_name, views, time_uploaded FROM info  WHERE name = :name;");
            $stmt->bindValue(':name', $person, SQLITE3_TEXT);
            $result = $stmt->execute();

            if ($result) 
            { // so no errors in the query
                while ($row = $result->fetchArray()) 
                { // while still a row to parse
                    //gets the url where the image is stored
                    $url = "https://www.pic.ucla.edu/~hinaljajal/HW7/uploads/" . $row['file_name'];
                    echo "<body>";
                    //color selected 
                    $color = $_POST['color'];
                    //sets background color to the selected color
                    echo "<body style = 'background-color: $color;'>";
                    //adds a query string to identify the file name 
                    $download = "download.php" . "?x=" . $row['file_name'];
                    //time of upload for the hover
                    $hover = $row['time_uploaded'];
                    $image_alt = $row['title'];
                    //displays the image with an href directing to download.php
                    echo "<a href=$download><img src = $url width = 400px alt='$image_alt' title='$hover'/></a>";
                    echo "<br>";
                    //prints the number of views 
                    $sentence = $row['title'] . " has " . $row['views'] . " view(s).";
                    echo "<b>$sentence</b>";
                    echo "<br>";

                }
            //updates the view count (increases by 1 each time)
            $statement = $mydb->prepare("UPDATE info SET views=views+1 WHERE name=:name");
            $statement->bindValue(':name', $person, SQLITE3_TEXT);
            $statement->execute();

            }

        }
        else //so no images for the person
        {
            //prints message
            echo "There are no files for user " . $person;
        }
    }
    $mydb->close();
}

?>
</html>