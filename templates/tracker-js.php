<?php defined( 'ABSPATH' ) or die(); ?>
<!-- Yandex.Metrika counter by Yandex Metrica Plugin -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter<?php echo $this->options["counter_id"];?> = new Ya.Metrika({id:<?php echo $this->options["counter_id"];?>,
                    webvisor:<?php echo $this->options["webvisor"]?"true":"false";?>,
                    clickmap:<?php echo $this->options["clickmap"]?"true":"false";?>,
                    trackLinks:<?php echo $this->options["tracklinks"]?"true":"false";?>,
                    accurateTrackBounce:<?php echo $this->options["accurate_track"]?"true":"false";?>,
                    trackHash:<?php echo $this->options["hash_track"]?"true":"false";?>});
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/<?php echo $this->options["counter_id"];?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter  -->