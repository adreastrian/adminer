<?php

class FixedFooter extends \Adminer\Plugin
{
    public function head()
    {
        ?>
        <script <?php echo \Adminer\nonce(); ?> type="text/javascript">
            $(document).ready(function () {
                var $footer = $('.footer');

                if ($footer.length) {
                    // append the import section to the footer
                    $footer.append($('.footer').next().addClass('footer-import'));
                }
            });
        </script>
        <?php
    }
}
