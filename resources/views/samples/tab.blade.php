<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 1:10 PM
 */ ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css"
          href="{{URL::asset('plugins/easy-responsive-tabs/css/easy-responsive-tabs.css')}}"/>
    <script src="{{URL::asset('plugins/easy-responsive-tabs/js/jquery-1.9.1.min.js')}}"></script>
    <script src="{{URL::asset('plugins/easy-responsive-tabs/js/easyResponsiveTabs.js')}}"></script>

    <style type="text/css" rel="stylesheet">
        body {
            background: #fff;
        }

        #container {
            width: 940px;
            margin: 0 auto;
        }

        @media only screen and (max-width: 768px) {
            #container {
                width: 90%;
                margin: 0 auto;
            }
        }
    </style>

    <title>Tab</title>

</head>
<body>
<div id="parentHorizontalTab">
    <ul class="resp-tabs-list hor_1">
        <li>Horizontal 1</li>
        <li>Horizontal 2</li>
        <li>Horizontal 3</li>
    </ul>
    <div class="resp-tabs-container hor_1">
        <div>
            <p>
                <!--vertical Tabs-->

            <div id="ChildVerticalTab_1">
                <ul class="resp-tabs-list ver_1">
                    <li>Responsive Tab 1</li>
                    <li>Responsive Tab 2</li>
                    <li>Responsive Tab 3</li>
                    <li>Long name Responsive Tab 4</li>
                </ul>
                <div class="resp-tabs-container ver_1">
                    <div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nibh urna, euismod ut ornare non, volutpat vel tortor. Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravida mollis.</p>
                    </div>
                    <div>
                        <p>Lorem ipsum dolor sit amet, lerisque commodo. Nam porta cursus lectusconsectetur adipiscing elit. Vestibulum nibh urna, euismod ut ornare non, volutpat vel tortor. Integer laoreet placerat suscipit. Sed sodales sce. Proin nunc erat, gravida a facilisis quis, ornare id lectus</p>
                    </div>
                    <div>
                        <p>Suspendisse blandit velit Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravid urna gravid eget erat suscipit in malesuada odio venenatis.</p>
                    </div>
                    <div>
                        <p>d ut ornare non, volutpat vel tortor. InLorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nibh urna, euismod ut ornare non, volutpat vel tortor. Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravida mollis.t in malesuada odio venenatis.</p>
                    </div>
                </div>
            </div>
            </p>
            <p>Tab 1 Container</p>
        </div>
        <div>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nibh urna, euismod ut ornare non, volutpat vel tortor. Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravida mollis.
            <br>
            <br>
            <p>Tab 2 Container</p>
        </div>
        <div>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nibh urna, euismod ut ornare non, volutpat vel tortor. Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravida mollis.
            <br>
            <br>
            <p>Tab 3 Container</p>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#parentHorizontalTab').easyResponsiveTabs({
            type: 'default', //Types: default, vertical, accordion
            width: 'auto', //auto or any width like 600px
            fit: true, // 100% fit in a container
            tabidentify: 'hor_1', // The tab groups identifier
            activate: function(event) { // Callback function if tab is switched
                var $tab = $(this);
            }
        });
        // Child Tab
        $('#ChildVerticalTab_1').easyResponsiveTabs({
            type: 'default',
            width: 'auto',
            fit: true,
            tabidentify: 'ver_1', // The tab groups identifier
            activetab_bg: '#fff', // background color for active tabs in this group
            inactive_bg: '#F5F5F5', // background color for inactive tabs in this group
            active_border_color: '#c1c1c1', // border color for active tabs heads in this group
            active_content_border_color: '#5AB1D0' // border color for active tabs contect in this group so that it matches the tab head border
        });
    });
</script>
</body>
</html>

