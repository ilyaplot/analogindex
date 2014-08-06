<!--
 [0] => Array
        (
            [id] => 1
            [name] => Iphone 5
            [manufacturer] => Array
                (
                    [id] => 1
                    [name] => Apple
                    [link] => apple
                )

            [link] => iphone5
            [price] => 25000
            [rating] => 99
            [likes] => 88
            [image] => Array
                (
                    [link] => iphone-5
                    [file] => 1
                    [ext] => png
                )

        )
-->

                                                    <div class="informer">
							<div class="informer-top">
								<div class="informer-t-left"><span class="informer-title">Смартфоны</span></div>
								<div class="informer-t-right">
									<nav id="informer-top-menu">
										<ul>
											<li class="active"><a href="#" class="informer-icon-rating"></a></li>
											<li id="like_li_informer"><a href="#" class="informer-icon-likes"></a></li>
											<li id="price_li_informer"><a href="#" class="informer-icon-price"></a>
												<div class="informer-curr-bl">
													<a href="#" class="informer_currency-select cur-rub"><span class="drpd_arrow-informer"></span></a>
													<ul>
														<li><a href="#" class="cur-dol"></a></li>
														<li><a href="#" class="cur-eur"></a></li>
													</ul>
												</div>
											</li>
										</ul>
									</nav>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>
							<div class="informer-c">
                                                            <ul class="informer-listGoods">
                                                                <?php foreach ($list as $item): ?>
                                                                <li>
                                                                    <div class="informer-listGoods_photo"><a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$item['link'], 'manufacturer'=>$item['manufacturer']['link'], 'language'=>  Language::getCurrentZone()))?>">
                                                                            <img src="<?php
                                                                            if (isset($item['image']['file']))
                                                                                echo Yii::app()->createUrl("site/download", array(
                                                                                    'id'=>@$item['image']['file'],
                                                                                    'filename'=>@$item['image']['link'],
                                                                                    'link'=>@$item['link'],
                                                                                    'language'=>  Language::getCurrentZone(),
                                                                                    'size' => ImagesModel::SIZE_SMALL
                                                                                )); 
                                                                            else
                                                                                echo "/assets/img/photo/informers/1.png";
                                        ?>" style="height:37px; width: 30px;" alt="<?php echo $item['manufacturer']['name']." ".$item['name'] ?>" /></a></div>
                                                                    <div class="informer-listGoods_desc"><a href="<?php echo Yii::app()->createUrl("site/goods", array('link'=>$item['link'], 'manufacturer'=>$item['manufacturer']['link'], 'language'=>  Language::getCurrentZone()))?>"><span><?php echo $item['manufacturer']['name']." ".$item['name'] ?></span></a></div>
                                                                    <div class="informer-listGoods_rating"><?php echo $item['rating']?></div>
                                                                    <div class="informer-listGoods_like"><?php echo $item['likes']?></div>
                                                                    <div class="informer-listGoods_price"><?php echo $item['price']?></div>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
							<div class="informer-bottom">
								<div class="informer-b-left"><img src="/assets/img/small/logo.png" height="17" width="79" alt=""></div>
								<div class="informer-b-right">
									<ul id="informer-b-r-links" class="clr">
										<li><a href="#" class="informer-link-share" title="Поделиться"></a>
										<div class="submenu">
											<nav class="clr">
												<a href="#" class="share_block-fb" title="Facebook"></a>
												<a href="#" class="share_block-gp" title="Google+"></a>
												<a href="#" class="share_block-tw" title="Twitter"></a>
												<a href="#" class="share_block-vk" title="Вконтакте"></a>
											</nav>
										</div></li>
										<li><a href="#" class="informer-link-send" title="Рассказать"></a></li>
									</ul>
								</div>
								<div class="clear"></div>
							</div>
						</div>