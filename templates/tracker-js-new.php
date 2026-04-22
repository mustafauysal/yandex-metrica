<?php
defined( 'ABSPATH' ) or die();

$counter_id          = absint( $this->options["counter_id"] );
$tracker_address     = ! empty( $this->options["tracker-address"] ) ? esc_url( $this->options["tracker-address"] ) : "https://mc.yandex.ru/metrika/tag.js";
$ecommerce_container = apply_filters( 'yandex_metrica_ecommerce_container_name', $this->options['ecommerce_container_name'] );
$noscript_img_base   = apply_filters( 'yandex_metrica_noscript_img_base', "https://mc.yandex.ru/watch/" );
?>
<!-- Yandex.Metrika counter by Yandex Metrica Plugin -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "<?php echo esc_js( $tracker_address ); ?>", "ym");

    ym(<?php echo $counter_id;?>, "init", {
        id:<?php echo $counter_id;?>,
        clickmap:<?php echo $this->options["clickmap"]?"true":"false";?>,
        trackLinks:<?php echo $this->options["tracklinks"]?"true":"false";?>,
        accurateTrackBounce:<?php echo $this->options["accurate_track"]?"true":"false";?>,
        webvisor:<?php echo $this->options["webvisor"] ? "true" : "false";?>,
	    <?php if($this->options['dispatch_ecommerce']):?>
        ecommerce: "<?php echo esc_js( $ecommerce_container );?>"
	    <?php endif;?>
    });
</script>
<noscript><div><img src="<?php echo esc_url( $noscript_img_base . $counter_id ); ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
