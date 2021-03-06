<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->pageTitle ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="<?php echo $this->pageDescription ?>" />
        <meta name="keywords" content="<?php echo $this->pageKeywords ?>" />
        <meta name='yandex-verification' content='60481e4daa763de4' />
        <meta name='yandex-verification' content='57ea89bb434b0eaf' />
        <meta name="google-site-verification" content="GvnDB4pCoqEMd_YGsLvuNeuYFMhwskJEX5QBnQU6v8I" />
        
        <!--[if lt IE 9]>
            <script src="js/html5.js"></script>
        <![endif]-->


        
        <link rel="shortcut icon" href="/assets/img/favicon.ico" />
            <link href="/assets/css/reset.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.min.css" rel="stylesheet" type="text/css" />
        
    <script type="text/javascript" src="/assets/js/selectivizr-min.js"></script>
    <script type="text/javascript" src="/assets/js/scripts.js"></script>
        
    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <section class="container">
                    <div class="auth-login">
                        <div class="flRight">
                            <?php echo Yii::t('main', '<a href="http://analogindex.com/lang.html">English version</a>') ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="header-bl2">
                        <div>
                            <div class="flLeft">
                                <a href="http://analogindex.<?php echo Language::getCurrentZone() ?>/" class="logo-link">
                                    <img src="/assets/img/logo.png" alt="">
                                </a>
                            </div>
                            <div class="flRight">
                                <form action="http://search.analogindex.<?php echo Language::getCurrentZone() ?>/" method="get" class="head-form-search">
                                    <input type="submit" class="submit-search-h" id="head_submit_search" title="<?php echo Yii::t('main', "Искать") ?>" value="">
                                    <input autocomplete="off" type="text" class="input-search" name="keyword" value="<?php echo htmlspecialchars(isset($_GET['keyword']) ? $_GET['keyword'] : '') ?>">
                                    <div class="clear"></div>
                                </form>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php $this->widget('application.widgets.MainNavWidget.MainNavWidget'); ?>
                </section>
            </header><!-- /header -->

            <section id="wrapper_c">

                <section class="container">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- analogindex_top -->
                    <center><ins class="adsbygoogle"
                                 style="display:inline-block;width:728px;height:90px"
                                 data-ad-client="ca-pub-7891165885018162"
                                 data-ad-slot="3491233138"></ins></center>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                    <?php echo $content ?>
                </section>
            </section>

            <footer id="footer">

                <!-- analogindex_bottom_responsive -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-7891165885018162"
                     data-ad-slot="2503694332"
                     data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                <section class="container">

                    <div class="f_menu">
                        <!--
                        <nav class="nav-footer">
                            <a href="#"><?php echo Yii::t('main', 'Информер на свой сайт') ?></a>
                            <a href="#"><?php echo Yii::t('main', 'Конструктор информеров') ?></a>
                            <a href="#"><?php echo Yii::t('main', 'Партнерская программа') ?></a>
                            <a href="#"><?php echo Yii::t('main', 'Пользовательское предложение') ?></a>
                            <a href="#"><?php echo Yii::t('main', 'Обратная связь') ?></a>
                        </nav>
                        -->
                    </div>
                    <p class="f_copyright">&quot;Analog Index&quot; &copy; 2014 - <?php echo date("Y") ?></p>
                </section>
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
            </footer>
        </div>
    </body>
    

</html>