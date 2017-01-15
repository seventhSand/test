<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:19 PM
 */ ?>

<html>
<head>
  <title>Form Generator</title>
  <style type="text/css">
    body {
      width: 20%;
      margin: 30px auto;
    }

    a {
      display: inline-block;
      padding: 10px;
      text-decoration: none;
      color: #333;
    }
  </style>
</head>
<body>
{?
  $l = Wa::html('table');
  $l->setTitle('Table 1');
  $l->addHead(function($head){
      $head->addRow()->addCell('name', ['rowspan' => 2])->addCell('month', ['colspan' => 4]);
      $head->addRow()->addCell('january')->addCell('february')->addCell('march')->addCell('april');
  });
  $l->addBody(function($body){
      $body->addRow()
          ->addCell('John Doe', ['style' => 'color:red'])
          ->addCell('20')->addCell('30')->addCell('30')->addCell('30');
      $body->addRow()
          ->addCell('Alan Doe')->addCell('25')->addCell('15')->addCell('30')->addCell('25');
      $body->addRow()->addCell('Sarah Doe')->addCell('20')->addCell('20')->addCell('30')->addCell('10');
  });
  $l->addFoot()->addRow('div')->addCell('Total')->addCell(65)->addCell(65)->addCell(65)->addCell(65);
?}
{!! $l->toHtml() !!}
<div style="text-align: center;margin-top: 30px;">
  {!! Html::link('form', 'Form') . Html::link('list', 'Listing') !!}
</div>
</body>
</html>