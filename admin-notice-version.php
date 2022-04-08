<div id="message" class="error">
    <p>
        <strong>
            <?php
            /**
             * Printf
             */
            \printf(
                \esc_html__('Sorry, Aruba HiSpeed Cache requires WordPress %s or higher.', 'aruba-hispeed-cache'),
                \esc_html($this->config::MINIMUM_WP)
            ); ?>
        </strong>
    </p>
</div>