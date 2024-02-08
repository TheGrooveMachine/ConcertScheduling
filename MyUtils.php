<?php
class MyUtils {
    static function html_header($title="Untitled") {
        $string = <<<END
            <!DOCTYPE html>
            <head>
                <!-- Your existing head content -->

                <!-- Add this link for the "Logout" option -->
                <link rel="stylesheet" type="text/css" href="assets/styles.css" />
                <link rel="stylesheet" type="text/css" href="assets/css.css" />
            </head>
            <header>
                <nav>
                    <!-- Your existing navigation content -->
                    <ul>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                    <section></section>
                </nav>
            </header>
            <body>\n
        END;
        return $string;
    }

    static function html_footer($text="") {
        $string ="\n$text\n</body>\n</html>";
        return $string;
    }

    
}
?>
