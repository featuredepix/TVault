<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="sha384-2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css">
    <title>TVault :: Calendar</title>
  </head>
  <body>
    <span style="display: none;" id="hiddenVariables">
      <span id="library"><?php echo require("get-library.php"); ?></span>
    </span>
    <span style="width:100vw;height:5vh;"><a style='border-radius:0;' href='/' class='btn btn-block btn-primary'>Go Back</a></span>
    <div id="calendar" style="padding:10px;height:85vh;width:100vw;"></div>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
    <script>
      function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
      }
      var userSelectedShows = $("span#hiddenVariables span#library").html().toString().split(",");
      $(document).ready(function(){
        /* Start Validating User Selected Shows */
        var validatedShows = [];
        var failedShows = [];
        jQuery.each(userSelectedShows, function(index,value){
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            async: false,
            success: function(data){
              validatedShows.push(data.id);
            },
            error: function(){
              failedShows.push(value);
            }
          });
        });
        /* Start Validating Ended Shows */
        var onGoingShows = [];
        var endedShows = [];
        jQuery.each(validatedShows, function(index,value){
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            async: false,
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
        toShow = onGoingShows;
        jQuery.each(toShow, function(index,value){
          var showInfo = null;
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString(),
            dataType: "json",
            async: false,
            success: function(data){
              showInfo = data;
            }
          });
          var showEpisodeDates = [];
          $.ajax({
            type: "GET",
            url: "http://api.tvmaze.com/shows/" + value.toString() + "/episodes",
            dataType: "json",
            async: false,
            success: function(data){
              $.each(data, function(index,value){
                showEpisodeDates.push([value.airdate, value.season + "x" + pad(value.number,2)]);
              });
            }
          });
          $("#calendar").fullCalendar({
            firstDay: 1,
            height: "parent",
            editable: false,
            eventColor: '#0275d8',
            eventTextColor: '#ffffff'
          });
          $.each(showEpisodeDates, function(index,value){
            var episodeEvent = new Object();
            episodeEvent.title = showInfo.name + " - " + value[1];
            episodeEvent.start = new Date(value[0]);
            episodeEvent.allDay = true;
            $("#calendar").fullCalendar('renderEvent',episodeEvent,true);
          });
        });
      });
    </script>
  </body>
</html>
