#!/usr/local/bin/php
<!DOCTYPE html>
<html lang="en">

<?php
if (isset($_POST['submit']))
{ // they did submit
    //the file name of the uploaded file
    $fileName = $_FILES['their_file']['name'];
    //gets the file base name (without the extension)
    $pieces = explode(".", $fileName);
    $base = $pieces[0];
    //converts to png
    $fileName = $base . ".png";
    //location where file will be saved
    $saveLocation = dirname(realpath(__FILE__)) . '/uploads/' . $fileName;

    try
    { // attempt to establish connection
        $mydb = new SQLite3('images.db'); // opens or creates the database
    }
    catch(Exception $ex)
    { // may throw
        echo $ex->getMessage();
    }    

    //creates table to store all the information
    $statement = 'CREATE TABLE IF NOT EXISTS info(name TEXT, title TEXT, file_name TEXT, views INTEGER, time_uploaded TEXT);';
    //runs the query
    $run = $mydb->query($statement);
    //gets the name of the user and the title of the image
    $name = $_POST['name'];
    $title = $_POST['title'];
    //views start at 0
    $views = 0;
    $file_name = $fileName;
    //time zone of the server (set to PST as was recommended by the professor)
    date_default_timezone_set('America/Los_Angeles');
    //gets the current time (upload time) in the desired format
    $time_uploaded = date("m/d/Y H:i");
    //creates the title
    echo "<head>";
    $page_title = "Thank You, " . $name . "!";
    echo "<title> $page_title </title>";
    echo "<meta charset='UTF-8'>";
    echo "</head>";

    //this statement counts the number of rows where an image with the same title and by the same person exist
    $stmt = $mydb->prepare("SELECT COUNT(1) FROM info WHERE name=:name AND title=:title;");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($result)  //if successful
    {
        $row = $result->fetchArray(); //gets the result
        if ($row[0] >= 1) //if the count is greater than or equal to 1 
        {
            //prints a message to let the user know that the image already exists
            $alert = "A photo named " . $title . " by " . $name . " already exists.";
            echo $alert;
        }
        else //if such a record doesn't already exist
        {
            //inserts the name, title, name of the file, number of views, and time of upload into the table
            $ins = $mydb->prepare("INSERT INTO info (name, title, file_name, views, time_uploaded) VALUES ( :name, :title, :file_name, :views, :time_uploaded );");
            $ins->bindValue(':name', $name, SQLITE3_TEXT);
            $ins->bindValue(':title', $title, SQLITE3_TEXT);
            $ins->bindValue(':file_name', $file_name, SQLITE3_TEXT);
            $ins->bindValue(':views', $views, SQLITE3_INTEGER);
            $ins->bindValue(':time_uploaded', $time_uploaded, SQLITE3_TEXT);
            $ins->execute();
            //prints message
            echo "Your image has been uploaded.";
            echo "<br>";
            //moves the file to the uploads folder
            move_uploaded_file($_FILES['their_file']['tmp_name'], $saveLocation);
            //shows the image just uploaded
            $url = "https://www.pic.ucla.edu/~hinaljajal/HW7/uploads/" . $fileName;
            echo "<img src = $url width = 400px alt = '$title'/>";

        }
    }
    //closes the connection
    $mydb->close();
}

?>
</html>
