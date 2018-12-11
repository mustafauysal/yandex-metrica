<?php defined( 'ABSPATH' ) or die(); ?>
<!-- Yandex.Metrika counter by Yandex Metrica Plugin -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "<?php echo( $this->options["tracker-address"] ? $this->options["tracker-address"] : "https://mc.yandex.ru/metrika/tag.js" ); ?>", "ym");

    ym(<?php echo $this->options["counter_id"];?>, "init", {
        id:<?php echo $this->options["counter_id"];?>,
        clickmap:<?php echo $this->options["clickmap"]?"true":"false";?>,
        trackLinks:<?php echo $this->options["tracklinks"]?"true":"false";?>,
        accurateTrackBounce:<?php echo $this->options["accurate_track"]?"true":"false";?>,
        webvisor:<?php echo $this->options["webvisor"]?"true":"false";?>
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/<?php echo $this->options["counter_id"];?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
