body
{
  margin:0px;
  padding:0px;
  background:#ffffff;
  font-size: 15px;
  font-family: Verdana, Arial, Sans-Serif;
}

table
{
  font-size: 1em;
}

/*--------------*/

td#td_sidebar
{
  padding: 0 5px 50px 5px;
  width: 200px;
  vertical-align: top;
  text-align: center;
}

td#td_content
{
  padding: 10px 10px 10px 10px;
  border-left: 1px black solid;
  vertical-align: top;
}

td.footer
{
  height: 30px;
  text-align: center;
  font-size: 0.85em;
  border-top: 1px black solid;
  border-bottom: 1px black solid;
}

.PoweredBy
{
  text-align: right;
  padding: 0px 10px 0px 0px;
  vertical-align: top;
}

.PoweredBy img
{
  position: relative;
  top: 4px;
}

/*------------------*/

a img {
 border: 0px;
}

div.linkbox
{
  padding: 0 25px 0 20px;
}

div.links
{
  border-bottom: 1px #CCC dashed;
}

div.links a {
  display: block;
  padding: 2px 10px 2px 10px;
  text-align: center;
  text-decoration: none;
  color: black;
  font-size: 1.2em;
  border-top: 1px #CCC dashed;
}

div.links a:hover {
  background: #D4DCFF;
}

div.linkbox span.header
{
  display: block;
  padding: 2px 10px 2px 10px;
  text-align: center;
  color: black;
  font-size: 1.0em;
  font-weight: bold;
}

/*-----------------*/

div.section
{
  padding: 5px 0 0 10px;
  margin: 0 0 15px 0;
}

div.section div.title
{
  border: 1px black solid;
  background: #DDDDFF;
  padding: 5px;
  font-weight: bold;
}

div.section div.content
{
  padding: 5px 0 0 10px;
  border-left: 1px black solid;
}

/*-----------------*/

div.section_noborder
{
  padding: 0px 0 0 0px;
  margin: 0 0 15px 0;
}

div.section_noborder div.title
{
  padding: 5px 5px 0px 0px;
  font-weight: bold;
}

div.section_noborder div.content
{
  padding: 0px 0 0 0px;
  clear: right;
}

/*-----------------*/


<?php
$settings = Array (
  "title_bg" => "",
  "menu_bg"=> ""
);

if ($site_config['template_data'] != "")
{
  $s = explode(";", $site_config['template_data']);
  foreach ($s as $ss)
  {
    $ss = explode(":",$ss);
    $settings[$ss[0]] = $ss[1];
  }
}

if ($settings['title_bg'] != "")
  echo "div.section div.title { background: ".$settings['title_bg']."; }\n";
if ($settings['menu_bg'] != "")
  echo "div.links a:hover { background: ".$settings['menu_bg']."; }\n";
?>
