<?php
session_start();
require_once '../controlers/adminControler.php';
require_once '../blades/header.php';
?>

<div id="bottom2" class="container">
    <div class="wrapper">
        <div class="btm2_con">
            <div style="width:800px; max-width:96%; margin: 30px auto;">
                <div class="widget-container classic-textwidget custom-classic-textwidget">
                    <div class="classic-text-widget">
                        <h3>
                            <h2>Customize Page Meta Tags</h2>
                        </h3><br>
                        <?php
                        foreach (glob("*.php") as $file) {
                            $name = ucfirst(str_replace(".php", "", basename($file)));
                            echo "<p style='margin-top:10px; font-weight: 500;color:#09c;'><a class='non_spa' href='" . base_path . $name . "/edit/true' style='coglor:#0f9c;'>" . $name . "</a></p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<?php
require_once '../blades/footer.php';
?>