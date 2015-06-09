<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

        <meta name="description" content="<?php echo $this->pageDescription ?>" />
        <meta name="keywords" content="<?php echo $this->pageKeywords ?>" />
        <meta name='yandex-verification' content='60481e4daa763de4' />
        <meta name='yandex-verification' content='57ea89bb434b0eaf' />
        <meta name="google-site-verification" content="GvnDB4pCoqEMd_YGsLvuNeuYFMhwskJEX5QBnQU6v8I" />
        <link rel="shortcut icon" href="/assets/img/favicon.ico" />
        <link href="/assets/materialize/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <!--<link type="text/css" rel="stylesheet" href="/assets/css/all.css"/>-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

        <title><?= $this->pageTitle ?></title>
    </head>
    <body>
        <div class="container">
            <?php $this->widget('application.widgets.materialize.Navigation'); ?>
            <?php $this->widget('application.widgets.materialize.Breadcrumbs', ['items' => $this->breadcrumbs]); ?>
            <?= $content ?>

        </div>
        <div class="container">
            <div class="row">
                <div class="col s12">
                    <div class="center-align">
                        <!-- materialize_analogindex_top -->
                        <?php $rnd = mt_rand(1, 5) ?>
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-<?= ($rnd <= 3) ? 9796705629555003 : 7891165885018162 ?>"
                             data-ad-slot="<?= ($rnd <= 3) ? 8430964235 : 7160561934 ?>"
                             data-ad-format="auto"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
                <div class="col s6">
                    <?php if (Yii::app()->language == 'en'): ?>
                        <a href="https://vk.com/analogindex_com">vk.com/analogindex_com</a><br />
                        <a href="https://twitter.com/analogindex_com" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @analogindex_com</a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = p + '://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, 'script', 'twitter-wjs');</script>
<?php else : ?>
                        <a href="https://vk.com/analogindex_ru">vk.com/analogindex_ru</a><br />
                        <a href="https://twitter.com/analogindex" class="twitter-follow-button" data-show-count="false" data-lang="ru">Читать @analogindex</a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = p + '://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, 'script', 'twitter-wjs');</script>
    <?php endif; ?>
                </div>
                <div class="col s6">
                    <div class="right-align">
                        "Analog Index" © 2014 - 2015
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>

    <script type="text/javascript" src="/assets/js/nav-menu-mob.js"></script>
    <script type="text/javascript" src="/assets/js/set-equal.js"></script>
    <script type="text/javascript" src="/assets/js/grid-to-list.js"></script>
    <script type="text/javascript" src="/assets/materialize/js/materialize.min.js"></script>
<?php foreach ($this->scripts as $script): ?>
        <script type="text/javascript" src="/assets/js/<?= $script ?>"></script>
<?php endforeach; ?>

    <script>
                        (function (i, s, o, g, r, a, m) {
                            i['GoogleAnalyticsObject'] = r;
                            i[r] = i[r] || function () {
                                (i[r].q = i[r].q || []).push(arguments)
                            }, i[r].l = 1 * new Date();
                            a = s.createElement(o),
                                    m = s.getElementsByTagName(o)[0];
                            a.async = 1;
                            a.src = g;
                            m.parentNode.insertBefore(a, m)
                        })
                                (window, document, 'script', 'http://www.google-analytics.com/analytics.js', 'ga');

                        ga('create', 'UA-51137680-1', 'analogindex.com');
                        ga('send', 'pageview');

    </script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function () {
                try {
                    w.yaCounter<?php echo (Yii::app()->language == 'ru') ? 25369559 : 28579576 ?> = new Ya.Metrika({id:<?php echo (Yii::app()->language == 'ru') ? 25369559 : 28579576 ?>,
                        webvisor: true,
                        clickmap: true,
                        trackLinks: true,
                        accurateTrackBounce: true});
                } catch (e) {
                }
            });

            var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () {
                        n.parentNode.insertBefore(s, n);
                    };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="//mc.yandex.ru/watch/<?php echo (Yii::app()->language == 'ru') ? 25369559 : 28579576 ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</html>
