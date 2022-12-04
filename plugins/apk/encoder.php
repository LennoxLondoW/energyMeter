<?php




if(isset($_POST['encode'])){
    echo "<p>".base64_encode(json_encode($_POST))."</p>";
}



?>


<div>
    <form method="post" action="encoder.php">
        <p>Server: <input type="text" name="Server"></p>
        <p>User: <input type="text" name="User"></p>
        <p>Password: <input type="text" name="Password"></p>
        <p>Db: <input type="text" name="Db"></p>
        <p><input type="Submit" value="encode" name="encode"></p>
    </form>
</div>
<h1>Paste this code to the file of your choice</h1>

<?php
    echo htmlspecialchars(file_get_contents("encoder.txt"));
?>

