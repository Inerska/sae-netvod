<?php
$html = <<<END
    <div class="nav">
        <ul>
        <li><a href="?action=add-series">Add Serie</a></li>
        <li><a href="?action=remove-series">Remove</a></li>
        <li><a href="?action=add-episode">Add Episode</a></li>
</ul>
           
    </div>
    <style>
        .nav {
            position: absolute;
            top: 200px;
            left: 93%;
            right: 0;
            bottom: -1000px;
            background-color: #1a1a1a;
            color: white;
            height: 100vh;
        }
</style>

END;
echo $html;

