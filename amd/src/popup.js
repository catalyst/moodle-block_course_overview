// Javascript module to popup dialogue

define(['jquery', 'jqueryui', 'core/config'], function($, UI, mdlconfig) {

    return {
        init: function() {

            // Dialogues on activity icons
            $(".dialogue").dialog({
                autoOpen: false,
                minWidth: 400,
                classes: {
                    'ui-dialog': 'course-overview-dialog'
                },
                closeText: '',
                modal: true
            });

            //opens the appropriate dialog
            $(".overview-icon").click(function () {

                //takes the ID of appropriate dialogue
                var id = $(this).data('id');

                //open dialogue
                $(id).dialog("open");
            });

        }
    };
});
