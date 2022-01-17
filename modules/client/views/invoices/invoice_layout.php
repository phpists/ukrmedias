<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css" _media="print">
            @page {
                size: A5 landscape;
                margin: 0px;
            }
            html, body {
                width: 210mm;
                height: 148mm;
                margin: 0px;
                padding: 0px;
                font-size:12px;
            }
            <?php echo file_get_contents(__DIR__ . '/style.css'); ?>
        </style>
    </head>
    <body style="border:1px dotted #f0f0f0;">
        <?php echo $content; ?>
    </body>
</html>