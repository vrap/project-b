$(function () {
    /*var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();*/

    $('#calendar-holder').fullCalendar({
        /** You can change value here (as event color, calendar column...) **/
        defaultView: 'month',
        selectable: true,
        lazyFetching: true,
        eventColor: '#3d4c67',
        eventTextColor: '#FFFFFF',
        weekends: true,
        /** You can change value here (as event color, calendar column...) **/
        header: {
            left: 'prev, next',
            center: 'title',
            right: 'month,basicWeek,basicDay,'
        },
        select: function(startDate, endDate, allDay) {
            $( "#dialog-create-events" ).dialog({
                buttons: {
                    Ajouter: function() {
                        startDate = startDate.getUTCFullYear() + "-" + (1 + startDate.getUTCMonth()) + "-" +  (1 + startDate.getUTCDate()) + " " + $("#start_time").val();
                        endDate = endDate.getUTCFullYear() + "-" + (1 + endDate.getUTCMonth()) + "-" +  (1 + endDate.getUTCDate()) + " " + $("#end_time").val();
                        var json = { name: $("#lesson_name").val(), startDate: startDate, endDate: endDate};
                        $.ajax({
                            url     : Routing.generate('agenda_add_lesson', { data: JSON.stringify(json) } ), 
                            type    : 'POST',
                            success : function(response){
                                if(response === true) {
                                    window.location.reload();
                                }
                            }
                         });
                    },
                    Annuler: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            return;
        },
        eventClick: function(calEvent, jsEvent, view) {
            var json = { name:calEvent.title, startDate:calEvent.start, endDate:calEvent.end };
            $("<div>Voulez-vous vraiment supprimer cette entrée ?</div>").dialog({
                buttons: {
                  Delete: function() {
                    $.ajax({
                          url     : Routing.generate('agenda_delete_lesson', { id:calEvent.id } ), 
                          type    : 'POST',
                          success : function(response){
                              if(response === true) {
                                  window.location.reload();
                              }
                          }
                      });
                    },
                  Cancel: function() {
                    $( this ).dialog( "close" );
                  }
                }
            });

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
