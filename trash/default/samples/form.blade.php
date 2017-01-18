<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:07 PM
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
  $f = new \Webarq\Manager\HTML\FormManager('http://submitter.com', 'frm-detail');
  $f->setTitle('Registration Form');
  $f->setElementLabelDefaultContainer(':webarq.form.default.label')
      ->setElementInfoDefaultContainer(':webarq.form.default.info');
  $f->addCollection('text', 'username')
      ->setContainer(':webarq.form.default.item');
  $f->addCollection('password', 'password')
      ->setLabel('Password')
      ->setInfo('Combine your password with punctuation mark, alphanumeric character');
  $f->addCollection('text', 'email', function ($input) {
      $input->setInfo('Insert your valid email');
  });
  $f->addCollectionGroup(
      [['text', 'full name'], null, 'Insert your name according to your ID Card'],
      ['text', 'sex', 'value', function ($input) {
        $input->setLabel('Gender');
      }]
  );
?}
{!! $f->toHtml() !!}
<div style="text-align: center; border-top: 3px solid #333;">
  {!! Html::link('form', 'Form') . Html::link('list', 'Listing') !!}
</div>
</body>
</html>