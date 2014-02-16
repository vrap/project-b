$(function () {
    /*var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();*/

     var calendar = $('#calendar-holder').fullCalendar({
        defaultView: 'month',
        selectable: true,
        selectHelper: true,
        lazyFetching: true,
        header: {
            left: 'prev, next',
            center: 'title',
            right: 'month,basicWeek,basicDay,'
        },
        select: function(startDate, endDate, allDay) {
            $( "#dialog" ).dialog({
                buttons: {
                    Create: function() {
                        startDate = startDate.getUTCFullYear() + "-" + (1 + startDate.getUTCMonth()) + "-" +  (1 + startDate.getUTCDate()) + " " + $("#start_time").val();
                        endDate = endDate.getUTCFullYear() + "-" + (1 + endDate.getUTCMonth()) + "-" +  (1 + endDate.getUTCDate()) + " " + $("#end_time").val();
                        var json = { name: $("#lesson_name").val(), startDate: startDate, endDate: endDate};
                        $.ajax({
                            url     : Routing.generate('speaker_add_lesson', { data: JSON.stringify(json) } ), 
                            type    : 'POST',
                            success : function(response){
                                if(response == true) {
                                    window.location.reload();
                                }
                            }
                         });
                    }
                }
            });
            return;
        },
        timeFormat: {
            // for agendaWeek and agendaDay
            agenda: 'h:mmt', // 5:00 - 6:30
            // for all other views
            '': 'H:mm{ - H:mm}'  // 7p
        },
        eventSources: [
            {
                url: Routing.generate('fullcalendar_loader'), 
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: {
                },
                error: function() {
                   alert('There was an error while fetching Google Calendar!');
                }
            }
        ]
    });
});
