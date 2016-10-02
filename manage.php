<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="sha384-2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">
    <title>TVault :: Manage</title>
  </head>
  <body>
    <span style="display: none;" id="hiddenVariables">
      <span id="library"><?php echo require("get-library.php"); ?></span>
    </span>
    <div class="container-fluid p-t-1">
      <div class="jumbotron p-t-3 p-b-1" id="topContain">
        <h1 class="display-3">TVault</h1>
        <p class="lead">The super simple TV show manager!</p>
        <hr class="m-y-2">
        <p>Created by Luke Carr</p>
      </div>
      <a class="btn btn-lg btn-primary" href="/">Back to Library</a>
      <div class="jumbotron m-t-2 p-y-2">
        <h1 class="display-4 m-b-2">Manage your Library <a class='btn btn-danger btn-lg' href='/clear-library.php'>Clear Library</a></h1>
        <div class="row">
          <div class="col-xs-12 col-md-6">
            <h5>Add a Show</h5>
            <p>When searching for shows, if an IMDB page exists than the show will have a link to it below.</p>
            <div class='row m-b-1'><div class='col-xs-8'><input id="searchField" type="text" class="form-control" placeholder="Search for a Show..."></div><div class='col-xs-4'><a style='color:white' class="btn btn-block btn-primary" onclick="showResults($('#searchField').val())">Search</a></div></div>
            <span class="search"><table></table></span>
          </div>
          <div class="col-xs-12 col-md-6">
            <h5>Remove a Show</h5>
            <span class="library"><table></table></span>
          </div>
        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
    <script>
      var userSelectedShows = $("span#hiddenVariables span#library").html().toString().split(",");
      $.each(userSelectedShows, function(index,value){
        $.ajax({
          type: "GET",
          url: "http://api.tvmaze.com/shows/" + value.toString(),
          dataType: "json",
          async: false,
          success: function(data){
            $("span.library table").append("<tr><td class='p-r-2'>" + data.name + " (" + data.premiered.split("-")[0] + ")</td><td><a href='/remove-show.php?id=" + data.id + "'>Remove</a></td></tr>");
          },
          error: function(){
            failedShows.push(value);
          }
        });
      });
      function showResults(searchTerm){
        $.ajax({
          type: "GET",
          url: "http://api.tvmaze.com/search/shows",
          dataType: "json",
          data: {q: searchTerm},
          success: function(data){
            $("span.search table").html("");
            $.each(data, function(index,value){
              if(value.show.externals.imdb != undefined){
                $("span.search table").append("<tr><td class='p-r-2'><a target='_blank' href='http://www.imdb.com/title/" + value.show.externals.imdb + "'>" + value.show.name + " (" + value.show.premiered.split("-")[0] + ")</a></td><td><a href='/add-show.php?id=" + value.show.id + "'>Add</a></td></tr>");
              } else {
                $("span.search table").append("<tr><td class='p-r-2'>" + value.show.name + " (" + value.show.premiered.split("-")[0] + ")</td><td><a href='/add-show.php?id=" + value.show.id + "'>Add</a></td></tr>");
              }
            });
          }
        });
      }
    </script>
  </body>
</html>
