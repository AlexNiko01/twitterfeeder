<p align="center">
    <h1>Test project on yii2 for working with twitter api</h1>
   
</p>

<p>
 <h3>Startup instruction</h3>
</p>
<ol>
<li> run composer update</li>
<li> Edit the file `config/db.php` with real data, for example:
<pre>
     return [
         'class' => 'yii\db\Connection',
         'dsn' => 'mysql:host=localhost;dbname=yii2basic',
         'username' => 'root',
         'password' => '1234',
         'charset' => 'utf8',
     ];
     </pre>
     </li>
<li>run <b>yii migrate</b></li>
</ol>

 <h3>Available endpoints</h3>
 
 <ul>
 <li>domain/twitter/add?id=...&user=..&secret=..</li>
 <li>domain/twitter/?id=...&secret=..</li>
 <li>domain/twitter/remove?id=...&user=..&secret=..</li>
 </ul>