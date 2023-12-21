
(function($){

    $(window).load(function() {
        setTimeout(function() {
             $('.loading').fadeOut("slow");
        }, 500);
    });


    $(document).ready(function() {

        $('.carousel').carousel(); // leave this
        $('[data-toggle="tooltip"]').tooltip(); // leave this

        var busy = false;

        $('#signupform').on("submit", function(e) {
            e.preventDefault();

            if (busy) {
              return;
            }

            var fname = $('#fname').val();
            var emailaddr = $('#email').val();
            var html = $('#htmlout').prop('checked') ? 1 : 0;

            $('.container2').slideDown();
            $('.container2').html("<div style='color:#905ea7'>Please wait just a moment...</div>");

            busy = true;

            $.post( "signup.php", {
                name: fname,
                email: emailaddr,
                html: html
            }).done(function(data) {
                var status = data.toString();
                $('.container2').html(status);
                busy = false;
            });
        });

    });

})(jQuery);