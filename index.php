<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Dynamic Template Engine - Gerador de Template</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
	<div class="container-fluid">
		<form role="form" action="index_template.php" method="POST">
		  <div class="row">
			<div class="col-md-6">
			  <div class="form-group">
				<label for="login">Seu login (your login):</label>
				<select id="login" name="login">
					<option value="user">user</option>
					<option value="users">users</option>
				</select>
			  </div>
			</div>
			<div class="col-md-6">
			  <div class="form-group">
				<label for="group">Seu grupo (your group):</label>
				<select id="group" name="group">
					<option value="member">member</option>
					<option value="members">members</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="row"></div>
		  <div class="row">
			<div class="col-md-6">
			  <div class="form-group">
				<label for="components">JSON componentes (json components):</label><br />
				<label for="login" style="width:100%;">Ex: <br />
				<pre style="width:100%;">
				{
					"nome do componentes":
					{
						"template":"&lt;input type="text" /&gt;"
					},
					......
				}
				</pre></label>
				<textarea type="text" class="form-control" style="height:150px;" id="components" name="components">{"Header":{"template":"<!doctype html><html><head><meta charset=\"utf-8\"><title>#TITLE#</title>#javascript##jquery##css#</head><body>#children#</body></html>"},"TextField": {"template": "<input type=\"#TYPE#\" name=\"#NAME#\" id=\"#ID#\" class=\"#CLASS#\" value=\"#VALUE#\" style=\"#STYLE#\" />"},"Line": {"template": "<p>#VALOR#</p>"},"Lista": {"template": "<ul>|[<li>#nome#</li>]|</ul>"},"Select": {"template": "<select name=\"#name#\">|[<option value=\"#id#\">#nome#</option>]|</select>"}}</textarea>
			  </div>
			</div>
			<div class="col-md-6">
			  <div class="form-group">
				<label for="template">JSON template (json template):</label><br />
				<label for="login" style="width:100%;">Ex: <br />
				<pre style="width:100%;">
				{
					"nome do componentes":
					{
						"atributos": {
							..
						},
						..
					},
					......
				}
				</pre></label>
				<textarea type="text" class="form-control" style="height:150px;" id="template" name="template">[{"Header": {"css": [{"archive": {"relCSS": "stylesheet","hrefCSS": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css","integrityCSS": "sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7","crossCSS": "anonymous"}}, {"archive": {"relCSS": "stylesheet","hrefCSS": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css","integrityCSS": "sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r","crossCSS": "anonymous"}}],"javascript": [{"archive": {"hrefScript": "http://code.jquery.com/jquery-1.9.1.min.js"}}, {"archive": {"hrefScript": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js","crossScript": "anonymous","integrityScript": "sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"}}],"atributes": {"TITLE": "Dynamic Template Engine - Gerador de Template"},"children": [{"TextField": {"access": {"login": "users","group": "member"},"atributes": {"ID": "fieldId","VALUE": "Hi"}}}, {"Line": {}}, {"TextField": {"access": {"login": "user","group": "member"},"atributes": {"ID": "fieldId2","VALUE": "Olá"}}}, {"Lista": {"data": {"values": [{"nome": "test"}, {"access": {"login": ["james", "users"],"group": "members"},"nome": "test 2"}]}}}, {"Select": {"data": {"values": [{"access": {"login": "user","group": "member"},"id": "1","nome": "How are you?"}, {"access": {"login": "users","group": ["member", "administrator"]},"id": "2","nome": "Como está você?"}]}}}]}}]</textarea>
			  </div>
			</div>
		  </div>
		  <div class="form-group">
			<label for="template">Valores de retorno do banco(FETCH_ASSOC) (database values(FETCH_ASSOC)):</label>
			<textarea type="text" class="form-control" id="array" name="array">array(array("id" => 1,"nome" => "Kesley"),array("id" => 2,"nome" => "Fred"))</textarea>
		  </div>
		  <div class="checkbox">
			<label><input type="checkbox" name="debug"> Modo debug (debug mode)</label>
		  </div>
		  <button type="submit" class="btn btn-default">Enviar</button>
		</form>
	</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	
	<script>
	$(document).ready(function() {
		var theJson = jQuery.parseJSON($("#template").val());
		$("#template").val(JSON.stringify(theJson,undefined, 2));

		var theJson = jQuery.parseJSON($("#components").val());
		$("#components").val(JSON.stringify(theJson,undefined, 2));
	});
	</script>
  </body>
</html>