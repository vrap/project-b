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
            var name = prompt('Event Title:');
            var startTime = prompt('Start date:');
            var endTime = prompt('end Date:');
            //startDate = startDate.getFullYear() + '-' + (startDate.getMonth()+1)+ '-' + startDate.getDate();
            //endDate = endDate.getFullYear() + '-' + (endDate.getMonth()+1)+ '-' + endDate.getDate();
            /*startDate = startDate.getUTCFullYear() + "-" + (1 + startDate.getUTCMonth()) + "-" +  (1 + startDate.getUTCDate()) + " " +
                    startDate.getUTCHours() + ":" + startDate.getUTCMinutes() + ":" + startDate.getUTCSeconds();
            endDate = endDate.getUTCFullYear() + "-" + (1 + endDate.getUTCMonth()) + "-" +  (1 + endDate.getUTCDate()) + " " +
                    endDate.getUTCHours() + ":" + endDate.getUTCMinutes() + ":" + endDate.getUTCSeconds();*/
            startDate = startDate.getUTCFullYear() + "-" + (1 + startDate.getUTCMonth()) + "-" +  (1 + startDate.getUTCDate()) + " " + startTime;
            endDate = endDate.getUTCFullYear() + "-" + (1 + endDate.getUTCMonth()) + "-" +  (1 + endDate.getUTCDate()) + " " + endTime;
            if (name) {
                calendar.fullCalendar('renderEvent', {
                    name: name,
                    startDate: startDate,
                    endDate: endDate,
                },
                true // make the event "stick"
                );
                /**
                 * ajax call to store event in DB
                 */
                jQuery.post(
                    
                    Routing.generate('speaker_add_lesson', { name: name, startDate: startDate, endDate: endDate} ), 
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
