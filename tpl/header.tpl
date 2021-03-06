{strip}
<!DOCTYPE html>
<HTML>
    <HEAD>
        <META charset="utf-8">
        <TITLE>{$title} - {$Name}</TITLE>
        <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
        <style>
            body, td { font-family: Arial; }
            table tr:hover {
                background-color: #68B3DB;
            }
            fieldset {
                display: inline-block;
            }
            .status1 a { text-decoration: none; color:black; }
            .status1 { background: #99ff99 {* gut - grün *} }
            .test-alter { background: #ffabb5 {* veraltet - rosa *} }
            .test-alter1 { background: #86ec86 {* gut - grün *} }
            tr .wouldbe1 { background: yellow; }
            tr .wouldbe-1 { background: black; }
            tr .wouldbe-1 a { text-decoration:none; color:white; }
            .status0 { background: #ff9999 {* schlecht - rot *} }
            .label { width: 82px; display: inline-block; }
            img {
                vertical-align: top;
            }
            iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
            #alt_langs {
			  margin: 0 0 1em 0;
              float: right;
            }
            #breadcrumbs {
              margin: 0 0 1em 0;
            }
            div.messages, div.status, div.warning, div.error {
              min-height: 21px;
              margin: 10px 0;
              border: 2px solid #ff7;
              padding: 5px 5px 5px 35px;
              color: #000;
              background-color: #ffc;
              background-image: url(bilder/messages-status.png);
              background-repeat: no-repeat;
              background-position: 5px 5px;
            }
            div.status {
              background-color: #ddf;
              border: 2px solid #aaf;
            }
            .iframe_container { width: 600px; height: 350px; padding: 0.5em; }
            .ui-resizable-helper { border: 75px solid #EFEFEF; margin: -75px; }
            #soll_no_longer_needed-button { opacity: 0.4; }
            #soll_no_longer_needed-button:hover { opacity: 1; }
            #soll_no_longer_needed-wrap { background-color: #ffcccc; display: inline-block; }
            #soll_no_longer_needed-wrap:hover { background-color: white; }
            form.run_project { display: inline }
            form.run_project label, form.run_project input { visibility: hidden; }
            form.run_project:hover label, form.run_project:hover input { visibility: visible; }
        </style>
        <script src="js/jquery.min.js"></script>
    </HEAD>
    <BODY bgcolor="#ffffff">
    {if $message}
      <div class="messages status">{$message}</div>
    {/if}
{/strip}
