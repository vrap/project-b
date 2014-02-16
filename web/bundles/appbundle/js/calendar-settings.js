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
            var name = prompt('Lesson name:');
            var startTime = prompt('Start Time (eg: 9:00):');
            var endTime = prompt('End  Time (eg: 16:00):');
            startDate = startDate.getUTCFullYear() + "-" + (1 + startDate.getUTCMonth()) + "-" +  (1 + startDate.getUTCDate()) + " " + startTime;
            endDate = endDate.getUTCFullYear() + "-" + (1 + endDate.getUTCMonth()) + "-" +  (1 + endDate.getUTCDate()) + " " + endTime;
            var json = { name: name, startDate: startDate, endDate: endDate};
            
            if (name) {
                calendar.fullCalendar('renderEvent', {
                    name: name,
                    startDate: startDate,
                    endDate: endDate
                },
                true // make the event "stick"
                );
                /**
                 * ajax call to store event in DB
                 */
                jQuery.post(
                    Routing.generate('speaker_add_lesson', { data: JSON.stringify(json) } ), 
                    {
                        name: name,
                        startDate: startDate,
                        endDate: endDate,
                    }
                );
            }
            window.location.reload();
            calendar.fullCalendar('unselect');
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
