// Javascript module to move courses around

define(['jquery', 'jqueryui'], function($, UI) {

    return {
        init: function() {

            // change non-js links to be inactive
            $(".course_title .move a .fa-arrows-v").removeClass("fa-arrows-v").addClass("fa-arrows");
            $(".course_title .move a").removeAttr("href");

            // Make the course list sort
            $(".course_list").sortable({
                update: function(event, ui) {
                    var kids = $(".course_list").children();
                    var sortorder = [];
                    $.each(kids, function(index, value) {
                       var id = value.getAttribute('id');
                       sortorder[index] = id.substring(7);
                    });

                    // send new sortorder
                    var data = {
                        sesskey : M.cfg.sesskey,
                        sortorder : sortorder
                    };
                    $.post(
                        M.cfg.wwwroot+'/blocks/course_overview/save.php', 
                        data
                    );
                }
            });
        }
    };
});
