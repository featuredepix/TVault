<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="sha384-2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <title>TVault</title>
    <style>
      img.card-img {
        -webkit-filter: brightness(0.3);
        filter: brightness(0.3);
      }
    </style>
  </head>
  <body>
    <span style="display: none;" id="hiddenVariables">
      <span id="library"><?php echo require("get-library.php"); ?></span>
      <span id="hideEnded"><?php echo (isset($_GET['hideEnded']) ? "yes" : "no"); ?></span>
    </span>
    <div class="container-fluid p-t-1">
      <div class="jumbotron p-t-3 p-b-1" id="topContain">
        <h1 class="display-3">TVault</h1>
        <p class="lead">The super simple TV show manager!</p>
        <hr class="m-y-2">
        <p>Created by Luke Carr</p>
      </div>
      <?php if(isset($_GET["hideEnded"])): ?>
        <a class="btn btn-lg btn-primary" href="/">Show Ended Shows</a>
      <?php else: ?>
        <a class="btn btn-lg btn-primary" href="/?hideEnded">Hide Ended Shows</a>
      <?php endif ?>
      <a class="btn btn-lg btn-primary" href="/calendar.php">Calendar View</a>
      <a class="btn btn-lg btn-primary" href="/manage.php">Manage Library</a>
      <div class="jumbotron m-t-2 p-y-2">
        <h1 class="display-4 m-b-2">Your Library</h1>
        <span class="shows">

        </span>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script>
      Array.prototype.clean = function(deleteValue) {
        for (var i = 0; i < this.length; i++) {
          if (this[i] == deleteValue) {
            this.splice(i, 1);
            i--;
          }
        }
        return this;
      };
      jQuery.extend( jQuery.fn.dataTableExt.oSort, {
          "non-empty-string-asc": function (str1, str2) {
              if(str1 == "")
                  return 1;
              if(str2 == "")
                  return -1;
              return ((str1 < str2) ? -1 : ((str1 > str2) ? 1 : 0));
          },

          "non-empty-string-desc": function (str1, str2) {
              if(str1 == "")
                  return 1;
              if(str2 == "")
                  return -1;
              return ((str1 < str2) ? 1 : ((str1 > str2) ? -1 : 0));
          }
      } );
      $(document).ready(function(){
        $("table").DataTable({
          columnDefs: [
            {type: 'non-empty-string', targets: 3},{targets:2,orderable:false}
          ],
          order: [[3,'asc']],
          paging: false
        });
      });
      jQuery.ajaxSetup({async:false});
      if($("span#hiddenVariables span#library").html() == ""){
        var userSelectedShows = [];
        $("div.jumbotron.m-t-2").append("<p class='lead'>Your library is currently empty! Add shows by clicking Manage Library above!</p>");
      } else {
        var userSelectedShows = $("span#hiddenVariables span#library").html().toString().split(",").clean();
        /* Start Validating User Selected Shows */
        var validatedShows = [];
        var failedShows = [];
        jQuery.each(userSelectedShows, function(index,value){
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            success: function(data){
              validatedShows.push(data.id);
            },
            error: function(){
              failedShows.push(value);
            }
          });
        });
        if(failedShows.length > 0){
          $("div.jumbotron#topContain").append("<div class='alert alert-danger' role='alert'><strong>Oops!</strong> You have " + failedShows.length.toString() + " TV show(s) that are invalid in your library! These are the invalid IDs: " + failedShows.toString() + "</div>");
        }
        if(validatedShows.length > 0){
          $("div.jumbotron#topContain").append("<div class='alert alert-success' role='alert'><strong>Hooray!</strong> Successfully loaded " + validatedShows.length + " TV show(s) into your library!</div>");
        }
        /* Start Validating Ended Shows */
        var onGoingShows = [];
        var endedShows = [];
        jQuery.each(validatedShows, function(index,value){
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            success: function(data){
              if(data.status.toString().toLowerCase() == "ended"){
                endedShows.push(data.id);
              } else{
                onGoingShows.push(data.id);
              }
            }
          });
        });
        var toShow = [];
        if($("span#hiddenVariables span#hideEnded").html() == "yes"){
          toShow = onGoingShows;
          if(endedShows.length > 0){
            $("div.jumbotron#topContain").append("<div class='alert alert-warning' role='alert'><strong>Notice:</strong> You have chosen to hide ended shows, so we have hidden " + endedShows.length + " TV shows(s) from your library!</div>");
          }
        } else{
          toShow = validatedShows;
        }
        $("h1.display-4").append(" <span class='tag tag-success'>" + onGoingShows.length.toString() + " Ongoing</span>");
        if(endedShows.length > 0){
          $("h1.display-4").append(" <span class='tag tag-warning'>" + endedShows.length.toString() + " Ended</span>");
        }
        var showsHtml = "<table class='table table-sm table-striped'>";
        showsHtml += "<thead>";
          showsHtml += "<tr><th>ID</th><th>Show</th><th>Schedule</th><th>Next Episode</th></tr>";
        showsHtml += "</thead>";
        showsHtml += "<tbody>";
        jQuery.each(toShow, function(index,value){
          var showHtml = "<tr>";
          var showInfo = null;
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            success: function(data){
              showInfo = data;
            }
          });
          var showEpisodeDates = [];
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString() + "/episodes",
            dataType: "json",
            success: function(data){
              $.each(data, function(index,value){
                showEpisodeDates.push(value.airdate);
              });
            }
          });
          var now = new Date();
          var closest = Infinity;
          var nextEpisode = "";
          var nextEpisodeHidden = "";
          showEpisodeDates.forEach(function(d) {
             var date = new Date(d);
             if (date >= now && date < closest) {
                closest = d;
             }
          });
          if(closest != Infinity && showInfo.status.toString().toLowerCase() != "ended"){
            closest = new Date(closest);
            nextEpisode = ('0' + closest.getDate()).slice(-2) + '/' + ('0' + (closest.getMonth()+1)).slice(-2) + '/' + closest.getFullYear();
            nextEpisodeHidden = closest.getFullYear() + ('0' + (closest.getMonth()+1)).slice(-2) + ('0' + closest.getDate()).slice(-2);
          } else {
            nextEpisode = "Unknown";
            nextEpisodeHidden = "99999999";
          }
          showHtml += "<th scope='row'>" + showInfo.id + "</th>";
          showHtml += "<td>" + showInfo.name + "</td>";
          if(showInfo.status.toString().toLowerCase() == "ended"){
            showHtml += "<td><span class='tag tag-danger'>Ended</span></td>";
          } else{
            showHtml += "<td><span class='tag tag-success'>" + showInfo.schedule.days[0].substring(0,3) + " " + showInfo.schedule.time + "</span></td>";
          }
          showHtml += "<td><span style='display: none'>" + nextEpisodeHidden + "</span>" + nextEpisode + "</td>";
          showHtml += "</tr>"
          showsHtml += showHtml;
        });
        showsHtml += "</tbody>";
        showsHtml += "</table>";
        $("span.shows").append(showsHtml);
      }
    </script>
  </body>
</html>
