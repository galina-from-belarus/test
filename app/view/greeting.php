<p>
    Hello, <?php
    if (isset($_SESSION['name'])) {
        echo $_SESSION['name'];
    }
    ?>!
</p>