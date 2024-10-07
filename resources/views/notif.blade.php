<div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"
        integrity="sha512-xi/RZRIF/S0hJ+yJJYuZ5yk6/8pCiRlEXZzoguSMl+vk2i3m6UjUO/WcZ11blRL/O+rnj94JRGwt/CHbc9+6EA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.core.min.js"
        integrity="sha512-d00Brs/+XQUUaO0Y9Uo8Vw63o7kS6ZcLM2P++17kALrI8oihAfL4pl1jQObeRBgv06j7xG0GHOhudAW0BdrycA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @script
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                window.Echo.private('App.Models.User.<?php echo $user; ?>')
                    .listen('.database-notifications.sent', (event) => {
                        var sound = new Howl({
                            src: ['{{ asset('notif.wav') }}'],
                            onplayerror: function() {
                                sound.once('unlock', function() {
                                    sound.play();
                                });
                            },
                        });
                        sound.play();
                    });
            });
        </script>
    @endscript
</div>
